(() => {
    const beam = document.getElementById('blBeam');
    const leftPan = document.getElementById('blLeft');
    const rightPan = document.getElementById('blRight');
    const tray = document.getElementById('blTray');
    const hint = document.getElementById('arHint');
    const mascot = document.getElementById('arMascot');
    const COLORS = ['#D1F875', '#A5F0FA', '#F8B17A', '#D176F1'];
    const SYMBOL = { '+': '+', '-': '−', '*': '×', '/': '÷' };
    const MAX_ANGLE = 10;
    const TILT = 3.2;

    let core;
    let target = 0;
    let label = '';
    let blocks = [];
    let round = 0;
    let solved = 0;
    let moves = 0;
    let lock = false;
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

    function makeTarget() {
        const r = Math.min(round, 8);
        if (round < 2) {
            const t = rnd(5, 9 + round * 3);
            return { value: t, text: String(t) };
        }
        const op = core.cfg.ops[rnd(0, core.cfg.ops.length - 1)];
        let a, b, t;
        if (op === '+') {
            a = rnd(3, 9 + r); b = rnd(3, 9 + r); t = a + b;
        } else if (op === '-') {
            a = rnd(9, 18 + r); b = rnd(2, a - 4); t = a - b;
        } else if (op === '*') {
            a = rnd(2, 5); b = rnd(2, 5); t = a * b;
        } else {
            b = rnd(2, 4); t = rnd(3, 6 + Math.floor(r / 2)); a = b * t;
        }
        return { value: t, text: a + ' ' + SYMBOL[op] + ' ' + b };
    }

    function split(total, parts, cap) {
        for (let tries = 0; tries < 80; tries++) {
            const out = [];
            let rest = total;
            for (let i = 0; i < parts - 1; i++) {
                const room = rest - (parts - 1 - i);
                out.push(rnd(1, Math.min(room, cap)));
                rest -= out[out.length - 1];
            }
            if (rest >= 1 && rest <= cap) {
                out.push(rest);
                return out;
            }
        }
        return split(total, parts + 1, cap);
    }

    function newRound() {
        clearLater();
        lock = false;
        const t = makeTarget();
        target = t.value;
        label = t.text;
        const cap = core.cfg.maxBlock;
        const k = round < 3 ? 2 : rnd(2, 3);
        const parts = split(target, k, cap);
        const total = Math.max(core.cfg.blocks, parts.length + 2);
        const values = parts.slice();
        while (values.length < total) {
            const d = rnd(1, cap);
            if (d !== target) values.push(d);
        }
        blocks = shuffle(values).map((v, i) => ({ id: i, v, color: COLORS[rnd(0, COLORS.length - 1)], onPan: false }));
        render();
    }

    function sum() {
        return blocks.reduce((acc, b) => acc + (b.onPan ? b.v : 0), 0);
    }

    function makeBlock(b, small) {
        const el = document.createElement('button');
        el.type = 'button';
        el.className = 'bl-block' + (small ? ' small' : '');
        el.style.background = b.color;
        el.textContent = b.v;
        el.addEventListener('click', () => toggle(b.id));
        return el;
    }

    function render() {
        leftPan.innerHTML = '';
        const t = document.createElement('div');
        t.className = 'bl-block target';
        t.textContent = label;
        leftPan.appendChild(t);

        rightPan.innerHTML = '';
        tray.innerHTML = '';
        blocks.forEach((b) => {
            if (b.onPan) rightPan.appendChild(makeBlock(b, true));
            else tray.appendChild(makeBlock(b, false));
        });
        const diff = sum() - target;
        const ang = Math.max(-MAX_ANGLE, Math.min(MAX_ANGLE, diff * TILT));
        beam.style.setProperty('--ang', ang + 'deg');
    }

    function toggle(id) {
        if (!core.playing() || lock) return;
        const b = blocks.find((x) => x.id === id);
        if (!b) return;
        b.onPan = !b.onPan;
        moves++;
        core.sfx('playClickSound');
        render();
        if (sum() === target) {
            lock = true;
            solved++;
            core.addScore(core.cfg.points);
            core.sfx('playCorrectAnswer');
            core.retrigger(mascot, 'happy', 500);
            core.retrigger(beam, 'ok', 900);
            core.popup(640, 150, 'equilibrado! +' + core.cfg.points, 'good');
            later(() => { round++; newRound(); }, 1000);
        }
    }

    ArcadeCore((api) => {
        core = api;
        return {
            reset() {
                clearLater();
                round = 0;
                solved = 0;
                moves = 0;
                newRound();
            },
            stop() {
                clearLater();
            },
            summary() {
                return {
                    title: 'tempo esgotado!',
                    msg: solved > 0 ? 'que equilíbrio incrível!' : 'quase! tente de novo.',
                    good: solved > 0,
                    stats: [
                        { label: 'equilíbrios', value: solved },
                        { label: 'trocas', value: moves },
                    ],
                };
            },
        };
    });
})();
