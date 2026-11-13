(() => {
    const grid = document.getElementById('mmGrid');
    const hint = document.getElementById('arHint');
    const mascot = document.getElementById('arMascot');
    const COLORS = ['#D1F875', '#A5F0FA', '#F8B17A', '#D176F1'];
    const SYMBOL = { '+': '+', '-': '−', '*': '×', '/': '÷' };
    const PAIRS = 3;

    let core;
    let cards = [];
    let open = [];
    let state = 'idle';
    let lock = false;
    let round = 0;
    let revealLeft = 0;
    let matched = 0;
    let totalPairs = 0;
    let attempts = 0;
    let rounds = 0;
    let pending = [];

    const rnd = (lo, hi) => Math.floor(Math.random() * (hi - lo + 1)) + lo;

    function shuffle(a) {
        for (let i = a.length - 1; i > 0; i--) {
            const j = rnd(0, i);
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function later(fn, ms) {
        const id = setTimeout(() => {
            pending = pending.filter((p) => p !== id);
            fn();
        }, ms);
        pending.push(id);
    }

    function clearLater() {
        pending.forEach(clearTimeout);
        pending = [];
    }

    function makeExpression() {
        const ops = core.cfg.ops;
        const op = ops[rnd(0, ops.length - 1)];
        const r = Math.min(round, 6);
        let a, b, result;
        if (op === '+') {
            a = rnd(2, 9 + r * 2); b = rnd(2, 9 + r * 2); result = a + b;
        } else if (op === '-') {
            a = rnd(6, 12 + r * 2); b = rnd(1, a - 1); result = a - b;
        } else if (op === '*') {
            const m = Math.min(9, 4 + r);
            a = rnd(2, m); b = rnd(2, m); result = a * b;
        } else {
            const m = Math.min(9, 3 + r);
            b = rnd(2, m); result = rnd(2, m); a = b * result;
        }
        return { text: a + ' ' + SYMBOL[op] + ' ' + b, result };
    }

    function makePairs() {
        const colors = shuffle(COLORS.slice());
        const out = [];
        const used = new Set();
        let guard = 0;
        while (out.length < PAIRS && guard++ < 200) {
            const e = makeExpression();
            if (used.has(e.result)) continue;
            used.add(e.result);
            out.push({ id: out.length, expr: e.text, result: e.result, color: colors[out.length] });
        }
        return out;
    }

    function buildCards() {
        const pairs = makePairs();
        const list = [];
        pairs.forEach((p) => {
            list.push({ pair: p.id, label: p.expr, color: p.color, kind: 'expr' });
            list.push({ pair: p.id, label: String(p.result), color: p.color, kind: 'result' });
        });
        cards = shuffle(list).map((c) => ({ ...c, up: false, done: false, el: null }));
        grid.innerHTML = '';
        cards.forEach((c, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'mm-card';
            b.style.setProperty('--c', c.color);
            const inner = document.createElement('span');
            inner.className = 'mm-inner';
            const front = document.createElement('span');
            front.className = 'mm-front ' + c.kind;
            front.textContent = c.label;
            const back = document.createElement('span');
            back.className = 'mm-back';
            back.textContent = '?';
            inner.appendChild(front);
            inner.appendChild(back);
            b.appendChild(inner);
            b.addEventListener('click', () => flipCard(i));
            grid.appendChild(b);
            c.el = b;
        });
        totalPairs = pairs.length;
    }

    function setUp(c, v) {
        c.up = v;
        c.el.classList.toggle('up', v);
    }

    function startRound() {
        clearLater();
        open = [];
        matched = 0;
        lock = false;
        buildCards();
        state = 'reveal';
        core.freeze(true);
        const secs = core.cfg.revealSeconds - round * core.cfg.revealStep;
        revealLeft = Math.max(core.cfg.revealMin, secs);
        hint.textContent = 'memorize!';
        later(() => cards.forEach((c) => setUp(c, true)), 60);
    }

    function endReveal() {
        cards.forEach((c) => setUp(c, false));
        state = 'play';
        core.freeze(false);
        hint.textContent = 'encontre os pares!';
    }

    function flipCard(i) {
        if (!core.playing() || state !== 'play' || lock) return;
        const c = cards[i];
        if (c.up || c.done) return;
        setUp(c, true);
        open.push(i);
        core.sfx('playClickSound');
        if (open.length < 2) return;

        attempts++;
        const [a, b] = open.map((k) => cards[k]);
        open = [];
        if (a.pair === b.pair) {
            a.done = b.done = true;
            a.el.classList.add('done');
            b.el.classList.add('done');
            matched++;
            core.addScore(core.cfg.pairPoints);
            core.popup(b.el.offsetLeft + grid.offsetLeft + 90, b.el.offsetTop + grid.offsetTop, '+' + core.cfg.pairPoints, 'good');
            core.sfx('playCorrectAnswer');
            core.retrigger(mascot, 'happy', 500);
            if (matched >= totalPairs) {
                lock = true;
                rounds++;
                core.addScore(core.cfg.roundBonus);
                core.popup(560, 110, 'rodada completa! +' + core.cfg.roundBonus, 'good');
                later(() => { round++; startRound(); }, 1100);
            }
        } else {
            lock = true;
            core.sfx('playIncorrectAnswer');
            a.el.classList.add('miss');
            b.el.classList.add('miss');
            later(() => {
                setUp(a, false);
                setUp(b, false);
                a.el.classList.remove('miss');
                b.el.classList.remove('miss');
                lock = false;
            }, 850);
        }
    }

    ArcadeCore((api) => {
        core = api;
        return {
            reset() {
                clearLater();
                round = 0;
                rounds = 0;
                attempts = 0;
                totalPairs = 0;
                state = 'idle';
                lock = false;
                open = [];
                buildCards();
                hint.textContent = 'prepare-se!';
            },
            go() {
                startRound();
            },
            update(dt) {
                if (state !== 'reveal') return;
                revealLeft -= dt;
                hint.textContent = 'memorize! ' + Math.max(1, Math.ceil(revealLeft));
                if (revealLeft <= 0) endReveal();
            },
            stop() {
                clearLater();
            },
            summary() {
                return {
                    title: 'tempo esgotado!',
                    msg: rounds > 0 ? 'que memória incrível!' : 'quase! tente de novo.',
                    good: rounds > 0,
                    stats: [
                        { label: 'rodadas', value: rounds },
                        { label: 'tentativas', value: attempts },
                    ],
                };
            },
        };
    });
})();
