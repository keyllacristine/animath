(() => {
    const cfg = window.INFINITE_CONFIG;
    const W = 1280;
    const H = 720;
    const GROUND = 700;
    const ITEM = 96;
    const RABBIT_H = 190;
    const RABBIT_SPEED = 560;
    const MIN_GAP = 260;
    const COUNT_STEP = 0.8;
    const SYMBOL = { '+': '+', '-': '−', '*': '×', '/': '÷' };
    const BEST_KEY = 'animath_infinite_best_' + (cfg.username || 'visitante');
    const OPS = (cfg.ops || []).filter((o) => SYMBOL[o]);

    // imagens de vida (opcional): lifeFull = coração cheio, lifeEmpty = vazio (sem ela, usa a cheia apagada)
    const LIFE = { full: cfg.lifeFull || '', empty: cfg.lifeEmpty || '' };

    const $ = (id) => document.getElementById(id);
    const stage = $('igStage');
    const field = $('igField');
    const rabbit = $('igRabbit');
    const flip = rabbit.querySelector('.ig-flip');
    const rabbitImg = rabbit.querySelector('img');
    const livesEl = $('igLives');
    const scoreEl = $('igScore');
    const timeEl = $('igTime');
    const timerEl = $('igTimer');
    const barEl = $('igBar');
    const questionEl = $('igQuestion');
    const countEl = $('igCount');
    const startOv = $('igStart');
    const pauseOv = $('igPause');
    const endOv = $('igEnd');
    const startBest = $('igStartBest');

    const s = {
        phase: 'idle',
        lives: cfg.lives,
        time: cfg.time,
        score: 0,
        hits: 0,
        errors: 0,
        streak: 0,
        untilCorrect: 1,
        spawnTimer: 0,
        cd: 0,
        cdShown: 0,
        x: W / 2,
        facing: 1,
        q: null,
        lastKey: '',
        lastX: -999,
        items: [],
        keys: { left: false, right: false },
        moving: false,
    };

    let rw = 150;
    let lastTs = 0;
    const timers = new WeakMap();

    // poses do avatar: idle (parado), catch (encostou no objeto), good / bad (resultado, congela um instante)
    const SPRITES = cfg.sprites || {};
    const CATCH_MS = 220;     // tempo no sprite "catch" antes de mostrar o resultado
    const RESULT_MS = 900;    // tempo congelado em "good" / "bad" antes de voltar ao idle
    let poseTimer = 0;

    Object.values(SPRITES).forEach((src) => { const im = new Image(); im.src = src; });   // pré-carrega

    function setPose(name) {
        const src = SPRITES[name];
        if (src && !rabbitImg.src.endsWith(src.split('/').pop())) rabbitImg.src = src;
    }

    function reactPose(result) {                 // result: 'good' | 'bad'
        clearTimeout(poseTimer);
        setPose('catch');
        poseTimer = setTimeout(() => {
            setPose(result);
            poseTimer = setTimeout(() => setPose('idle'), RESULT_MS);
        }, CATCH_MS);
    }

    function resetPose() {
        clearTimeout(poseTimer);
        setPose('idle');
    }

    const rnd = (lo, hi) => Math.floor(Math.random() * (hi - lo + 1)) + lo;
    const pick = (list) => list[rnd(0, list.length - 1)];
    const sfx = (name) => { try { window.soundManager?.[name]?.(); } catch (e) { return; } };
    const show = (el) => el.classList.add('on');
    const hide = (el) => el.classList.remove('on');

    function retrigger(el, cls, ms) {
        el.classList.remove(cls);
        void el.offsetWidth;
        el.classList.add(cls);
        clearTimeout(timers.get(el));
        timers.set(el, setTimeout(() => el.classList.remove(cls), ms));
    }

    function loadBest() {
        try { return parseInt(localStorage.getItem(BEST_KEY), 10) || 0; } catch (e) { return 0; }
    }

    function saveBest(v) {
        try { localStorage.setItem(BEST_KEY, String(v)); } catch (e) { return; }
    }

    function fit() {
        const k = Math.min(window.innerWidth / W, window.innerHeight / H);
        stage.style.transform = 'translate(-50%, -50%) scale(' + k + ')';
    }

    function measureRabbit() {
        rw = rabbit.offsetWidth || rw;
    }

    function distractors(answer) {
        const set = new Set();
        const spread = Math.max(3, Math.round(answer * 0.3));
        for (let g = 0; set.size < 8 && g < 300; g++) {
            const d = rnd(1, spread);
            const v = answer + (Math.random() < 0.5 ? -d : d);
            if (v >= 0 && v !== answer) set.add(v);
        }
        for (let k = 1; set.size < 8; k++) set.add(answer + k);
        return [...set];
    }

    function makeQuestion() {
        const lvl = Math.min(cfg.maxLevel, Math.floor(s.hits / cfg.levelEvery));
        const hi = cfg.max + lvl * cfg.maxStep;
        const f = Math.min(cfg.factorCap, 5 + lvl);
        let a, b, answer, op, key, tries = 0;
        do {
            op = pick(OPS);
            if (op === '+') {
                a = rnd(cfg.min, hi); b = rnd(cfg.min, hi); answer = a + b;
            } else if (op === '-') {
                a = rnd(cfg.min, hi); b = rnd(cfg.min, hi);
                if (b > a) [a, b] = [b, a];
                if (a === b) a++;
                answer = a - b;
            } else if (op === '*') {
                a = rnd(2, f); b = rnd(2, f); answer = a * b;
            } else {
                b = rnd(2, f); answer = rnd(2, f); a = b * answer;
            }
            key = a + op + b;
        } while (key === s.lastKey && ++tries < 20);
        s.lastKey = key;
        return { text: a + ' ' + SYMBOL[op] + ' ' + b + ' = ?', answer, distractors: distractors(answer) };
    }

    function newQuestion() {
        s.q = makeQuestion();
        s.untilCorrect = rnd(cfg.correctAfterMin, cfg.correctAfterMax);
        questionEl.textContent = s.q.text;
        retrigger(questionEl, 'swap', 400);
    }

    // um ícone de vida: imagem (se configurada) ou o coração de texto
    function lifeIcon(full) {
        const textHeart = () => {
            const h = document.createElement('span');
            h.textContent = full ? '♥' : '♡';
            h.className = full ? 'full' : 'empty';
            return h;
        };
        const src = full ? LIFE.full : (LIFE.empty || LIFE.full);
        if (!src) return textHeart();

        const img = document.createElement('img');
        img.src = src;
        img.alt = '';
        img.className = full ? 'full' : 'empty';
        if (!full && !LIFE.empty) img.classList.add('dim');          // sem imagem de vida vazia: cheia apagada
        img.addEventListener('error', () => img.replaceWith(textHeart()));   // arquivo sumiu: volta ao ♥
        return img;
    }

    function renderLives() {
        livesEl.innerHTML = '';
        for (let i = 0; i < cfg.lives; i++) {
            livesEl.appendChild(lifeIcon(i < s.lives));
        }
    }

    function renderTime() {
        const t = Math.max(0, Math.ceil(s.time));
        timeEl.textContent = Math.floor(t / 60) + ':' + String(t % 60).padStart(2, '0');
        barEl.style.transform = 'scaleX(' + Math.max(0, s.time / cfg.time) + ')';
        timerEl.classList.toggle('urgent', s.time <= 10 && s.phase === 'playing');
    }

    function renderHud() {
        scoreEl.textContent = s.score;
        renderLives();
        renderTime();
    }

    function clearItems() {
        s.items.forEach((it) => {
            it.el.classList.add('gone');
            setTimeout(() => it.el.remove(), 250);
        });
        s.items = [];
    }

    function popup(x, y, text, kind) {
        const p = document.createElement('div');
        p.className = 'ig-pop ' + kind;
        if (LIFE.full && text.includes('♥')) {                       // "−1 ♥" com a imagem de vida
            p.append(text.replace('♥', '').trim() + ' ');
            const im = document.createElement('img');
            im.src = LIFE.full;
            im.alt = '';
            im.addEventListener('error', () => im.replaceWith('♥'));
            p.append(im);
        } else {
            p.textContent = text;
        }
        p.style.left = Math.max(20, Math.min(W - 140, x - 40)) + 'px';
        p.style.top = Math.max(60, y) + 'px';
        field.appendChild(p);
        setTimeout(() => p.remove(), 800);
    }

    function resetGame() {
        clearItems();
        s.lives = cfg.lives;
        s.time = cfg.time;
        s.score = 0;
        s.hits = 0;
        s.errors = 0;
        s.streak = 0;
        s.spawnTimer = 0.5;
        s.x = W / 2;
        s.facing = 1;
        s.lastKey = '';
        s.lastX = -999;
        s.keys.left = false;
        s.keys.right = false;
        flip.style.transform = 'scaleX(1)';
        resetPose();
        placeRabbit();
        newQuestion();
        renderHud();
    }

    function beginRound() {
        resetGame();
        hide(startOv);
        hide(endOv);
        hide(pauseOv);
        s.cd = COUNT_STEP * 3;
        s.cdShown = 0;
        s.phase = 'countdown';
        show(countEl);
    }

    function pickX() {
        const recent = s.items.filter((i) => i.y < 420).map((i) => i.x);
        let best = rnd(40, W - 40 - ITEM);
        let bestGap = -1;
        for (let i = 0; i < 40; i++) {
            const x = rnd(40, W - 40 - ITEM);
            const gap = Math.min(Math.abs(x - s.lastX), ...recent.map((rx) => Math.abs(x - rx)));
            if (gap >= MIN_GAP) return x;
            if (gap > bestGap) { bestGap = gap; best = x; }
        }
        return best;
    }

    function spawn() {
        const hasCorrect = s.items.some((i) => i.correct);
        const wantCorrect = s.untilCorrect <= 0 && !hasCorrect;
        let value;
        if (wantCorrect) {
            value = s.q.answer;
            s.untilCorrect = rnd(cfg.correctAfterMin + 1, cfg.correctAfterMax + 1);
        } else {
            value = pick(s.q.distractors);
            s.untilCorrect = Math.max(0, s.untilCorrect - 1);
        }
        const x = pickX();
        s.lastX = x;

        const el = document.createElement('div');
        el.className = 'ig-item c' + rnd(0, 3) + (String(value).length >= 3 ? ' l3' : '');
        el.textContent = value;
        field.appendChild(el);

        const speed = Math.min(cfg.speedMax, cfg.speed + s.hits * cfg.speedStep);
        s.items.push({ el, x, y: -ITEM, vy: speed * (0.9 + Math.random() * 0.25), value, correct: wantCorrect });
    }

    function onCorrect(it) {
        s.hits++;
        s.streak++;
        s.score += cfg.points;
        popup(it.x + ITEM / 2, it.y, '+' + cfg.points, 'good');
        retrigger(rabbit, 'happy', 500);
        reactPose('good');
        sfx('playCorrectAnswer');
        it.el.classList.add('gone');
        setTimeout(() => it.el.remove(), 250);
        s.items = s.items.filter((i) => i !== it);
        clearItems();
        newQuestion();
        s.spawnTimer = cfg.spawn * 0.5;
        scoreEl.textContent = s.score;
    }

    function onWrong(it) {
        s.lives--;
        s.errors++;
        s.streak = 0;
        popup(it.x + ITEM / 2, it.y, '−1 ♥', 'bad');
        retrigger(rabbit, 'hurt', 500);
        reactPose('bad');
        retrigger(field, 'shake', 400);
        retrigger($('igFlash'), 'on', 450);
        sfx('playIncorrectAnswer');
        it.el.remove();
        s.items = s.items.filter((i) => i !== it);
        renderLives();
        if (s.lives <= 0) endGame('lives');
    }

    function endGame(reason) {
        s.phase = 'over';
        clearItems();
        s.keys.left = false;
        s.keys.right = false;
        setMoving(false);
        timerEl.classList.remove('urgent');
        const best = loadBest();
        const record = s.score > best;
        if (record) saveBest(s.score);

        $('igEndTitle').textContent = reason === 'time' ? 'tempo esgotado!' : 'acabaram as vidas!';
        $('igEndMsg').textContent = record
            ? 'novo recorde! que incrível!'
            : (reason === 'time' ? 'você foi muito ' + (cfg.gender === 'f' ? 'rápida' : 'rápido') + '!' : 'não desanima, tente de novo!');
        $('igEndScore').textContent = s.score;
        $('igEndHits').textContent = s.hits;
        $('igEndErrors').textContent = s.errors;
        $('igEndBest').textContent = Math.max(best, s.score);
        show(endOv);
        if (reason === 'time') sfx('playLevelComplete');
    }

    function togglePause() {
        if (s.phase === 'playing') {
            s.phase = 'paused';
            s.keys.left = false;
            s.keys.right = false;
            setMoving(false);
            show(pauseOv);
        } else if (s.phase === 'paused') {
            s.phase = 'playing';
            hide(pauseOv);
        }
    }

    function setMoving(v) {
        if (s.moving === v) return;
        s.moving = v;
        rabbit.classList.toggle('moving', v);
    }

    function placeRabbit() {
        rabbit.style.transform = 'translate3d(' + (s.x - rw / 2) + 'px, 0, 0)';
    }

    function moveRabbit(dt) {
        const dir = (s.keys.right ? 1 : 0) - (s.keys.left ? 1 : 0);
        if (dir !== 0) {
            s.facing = dir;
            s.x = Math.max(rw / 2 + 10, Math.min(W - rw / 2 - 10, s.x + dir * RABBIT_SPEED * dt));
            flip.style.transform = 'scaleX(' + dir + ')';
        }
        setMoving(dir !== 0);
        placeRabbit();
    }

    function updateItems(dt) {
        const rl = s.x - rw * 0.4;
        const rr = s.x + rw * 0.4;
        const rt = GROUND - RABBIT_H * 0.8;
        for (const it of [...s.items]) {
            if (!s.items.includes(it)) continue;
            it.y += it.vy * dt;
            it.el.style.transform = 'translate3d(' + it.x + 'px, ' + it.y + 'px, 0)';
            const hit = it.x + ITEM - 14 > rl && it.x + 14 < rr && it.y + ITEM - 14 > rt && it.y + 14 < GROUND;
            if (hit) {
                if (it.correct) onCorrect(it);
                else onWrong(it);
                if (s.phase !== 'playing') return;
                if (it.correct) return;
            } else if (it.y > H) {
                it.el.remove();
                s.items = s.items.filter((i) => i !== it);
            }
        }
    }

    function update(dt) {
        if (s.phase === 'countdown') {
            moveRabbit(dt);
            s.cd -= dt;
            const n = Math.ceil(s.cd / COUNT_STEP);
            if (n !== s.cdShown && n > 0) {
                s.cdShown = n;
                countEl.textContent = n;
                retrigger(countEl, 'tick', 700);
            }
            if (s.cd <= 0) {
                s.phase = 'playing';
                countEl.textContent = 'vai!';
                retrigger(countEl, 'tick', 700);
                setTimeout(() => { if (s.phase !== 'countdown') hide(countEl); }, 650);
            }
            return;
        }
        if (s.phase !== 'playing') return;

        moveRabbit(dt);
        s.time -= dt;
        if (s.time <= 0) {
            s.time = 0;
            renderTime();
            endGame('time');
            return;
        }
        renderTime();

        s.spawnTimer -= dt;
        if (s.spawnTimer <= 0) {
            spawn();
            s.spawnTimer = Math.max(cfg.spawnMin, cfg.spawn - s.hits * cfg.spawnStep);
        }
        updateItems(dt);
    }

    function frame(ts) {
        const dt = Math.min(0.05, (ts - lastTs) / 1000 || 0);
        lastTs = ts;
        update(dt);
        requestAnimationFrame(frame);
    }

    function keyState(e, down) {
        if (e.code === 'ArrowLeft' || e.code === 'KeyA') { s.keys.left = down; return true; }
        if (e.code === 'ArrowRight' || e.code === 'KeyD') { s.keys.right = down; return true; }
        return false;
    }

    window.addEventListener('keydown', (e) => {
        const settings = document.getElementById('settings-overlay');
        if (settings && !settings.hidden) return;
        if (keyState(e, true)) {
            e.preventDefault();
            return;
        }
        if (e.code === 'Escape' || e.code === 'KeyP') {
            togglePause();
            return;
        }
        if ((e.code === 'Enter' || e.code === 'Space') && !(document.activeElement && document.activeElement.tagName === 'BUTTON')) {
            if (s.phase === 'idle' || s.phase === 'over') beginRound();
            else if (s.phase === 'paused') togglePause();
            e.preventDefault();
        }
    }, true);

    window.addEventListener('keyup', (e) => { keyState(e, false); });

    window.addEventListener('blur', () => {
        s.keys.left = false;
        s.keys.right = false;
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden && s.phase === 'playing') togglePause();
    });

    window.addEventListener('resize', fit);

    function holdButton(id, side) {
        const b = $(id);
        const on = (e) => { e.preventDefault(); s.keys[side] = true; };
        const off = () => { s.keys[side] = false; };
        b.addEventListener('pointerdown', on);
        b.addEventListener('pointerup', off);
        b.addEventListener('pointerleave', off);
        b.addEventListener('pointercancel', off);
    }

    function bindClick(id, fn) {
        $(id).addEventListener('click', (e) => {
            sfx('playClickSound');
            e.currentTarget.blur();
            fn();
        });
    }

    holdButton('igLeft', 'left');
    holdButton('igRight', 'right');
    bindClick('igStartBtn', beginRound);
    bindClick('igAgainBtn', beginRound);
    bindClick('igRestartBtn', beginRound);
    bindClick('igResumeBtn', togglePause);
    bindClick('igPauseBtn', togglePause);
    bindClick('igSoundBtn', () => window.openSettings());

    rabbitImg.addEventListener('load', () => { measureRabbit(); placeRabbit(); });

    fit();
    measureRabbit();
    s.q = null;
    resetGame();
    startBest.textContent = loadBest();
    show(startOv);
    requestAnimationFrame(frame);
})();