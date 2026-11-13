window.ArcadeCore = (makeHooks) => {
    const cfg = window.ARCADE_CONFIG;
    const W = 1280;
    const H = 720;
    const COUNT_STEP = 0.8;
    const BEST_KEY = 'animath_arcade_best_' + cfg.key + '_' + (cfg.username || 'visitante');

    const $ = (id) => document.getElementById(id);
    const stage = $('igStage');
    const field = $('igField');
    const scoreEl = $('igScore');
    const timeEl = $('igTime');
    const timerEl = $('igTimer');
    const barEl = $('igBar');
    const countEl = $('igCount');
    const startOv = $('igStart');
    const pauseOv = $('igPause');
    const endOv = $('igEnd');
    const timers = new WeakMap();

    const s = { phase: 'idle', time: cfg.time, score: 0, frozen: false, cd: 0, cdShown: 0 };
    let lastTs = 0;

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

    function renderScore() {
        scoreEl.textContent = s.score;
    }

    function renderTime() {
        const t = Math.max(0, Math.ceil(s.time));
        timeEl.textContent = Math.floor(t / 60) + ':' + String(t % 60).padStart(2, '0');
        barEl.style.transform = 'scaleX(' + Math.max(0, s.time / cfg.time) + ')';
        timerEl.classList.toggle('urgent', s.time <= 10 && s.phase === 'playing');
    }

    function popup(x, y, text, kind) {
        const p = document.createElement('div');
        p.className = 'ig-pop ' + kind;
        p.textContent = text;
        p.style.left = Math.max(20, Math.min(W - 160, x - 40)) + 'px';
        p.style.top = Math.max(60, y) + 'px';
        field.appendChild(p);
        setTimeout(() => p.remove(), 800);
    }

    function addScore(n) {
        s.score += n;
        renderScore();
    }

    function begin() {
        s.score = 0;
        s.time = cfg.time;
        s.frozen = false;
        hooks.reset();
        renderScore();
        renderTime();
        hide(startOv);
        hide(endOv);
        hide(pauseOv);
        s.cd = COUNT_STEP * 3;
        s.cdShown = 0;
        s.phase = 'countdown';
        show(countEl);
    }

    function finish(reason) {
        if (s.phase === 'over') return;
        s.phase = 'over';
        timerEl.classList.remove('urgent');
        if (hooks.stop) hooks.stop();
        const sum = hooks.summary(reason);
        const best = loadBest();
        const record = s.score > best;
        if (record) saveBest(s.score);

        $('igEndTitle').textContent = sum.title;
        $('igEndMsg').textContent = record ? 'novo recorde! que incrível!' : sum.msg;
        const box = $('igEndStats');
        box.innerHTML = '';
        const rows = [
            { label: 'pontos', value: s.score, cls: 'score' },
            { label: 'recorde', value: Math.max(best, s.score), cls: 'best' },
        ].concat(sum.stats || []);
        rows.forEach((r) => {
            const d = document.createElement('div');
            d.className = 'ig-stat ' + (r.cls || '');
            const b = document.createElement('b');
            b.textContent = r.value;
            d.appendChild(b);
            d.appendChild(document.createTextNode(r.label));
            box.appendChild(d);
        });
        show(endOv);
        if (sum.good) sfx('playLevelComplete');
    }

    function togglePause() {
        if (s.phase === 'playing') {
            s.phase = 'paused';
            show(pauseOv);
            if (hooks.onPause) hooks.onPause(true);
        } else if (s.phase === 'paused') {
            s.phase = 'playing';
            hide(pauseOv);
            if (hooks.onPause) hooks.onPause(false);
        }
    }

    function update(dt) {
        if (s.phase === 'countdown') {
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
                if (hooks.go) hooks.go();
            }
            return;
        }
        if (s.phase !== 'playing') return;
        if (!s.frozen) {
            s.time -= dt;
            if (s.time <= 0) {
                s.time = 0;
                renderTime();
                finish('time');
                return;
            }
            renderTime();
        }
        if (hooks.update) hooks.update(dt);
    }

    function frame(ts) {
        const dt = Math.min(0.05, (ts - lastTs) / 1000 || 0);
        lastTs = ts;
        update(dt);
        requestAnimationFrame(frame);
    }

    window.addEventListener('keydown', (e) => {
        const settings = document.getElementById('settings-overlay');
        if (settings && !settings.hidden) return;
        if (e.code === 'Escape' || e.code === 'KeyP') {
            togglePause();
            return;
        }
        if ((e.code === 'Enter' || e.code === 'Space') && !(document.activeElement && document.activeElement.tagName === 'BUTTON')) {
            if (s.phase === 'idle' || s.phase === 'over') begin();
            else if (s.phase === 'paused') togglePause();
            e.preventDefault();
        }
    }, true);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden && s.phase === 'playing') togglePause();
    });

    window.addEventListener('resize', fit);

    function bindClick(id, fn) {
        $(id).addEventListener('click', (e) => {
            sfx('playClickSound');
            e.currentTarget.blur();
            fn();
        });
    }

    bindClick('igStartBtn', begin);
    bindClick('igAgainBtn', begin);
    bindClick('igRestartBtn', begin);
    bindClick('igResumeBtn', togglePause);
    bindClick('igPauseBtn', togglePause);
    bindClick('igSoundBtn', () => window.openSettings());

    const api = {
        field,
        cfg,
        rnd,
        pick,
        sfx,
        retrigger,
        popup,
        addScore,
        finish,
        playing: () => s.phase === 'playing',
        phase: () => s.phase,
        timeLeft: () => s.time,
        freeze: (v) => { s.frozen = v; },
    };
    const hooks = makeHooks(api);

    fit();
    hooks.reset();
    renderScore();
    renderTime();
    $('igStartBest').textContent = loadBest();
    show(startOv);
    requestAnimationFrame(frame);

    return api;
};
