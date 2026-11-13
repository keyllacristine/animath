(() => {
    const boardEl = document.getElementById('sdBoard');
    const padEl = document.getElementById('sdPad');
    const mascot = document.getElementById('arMascot');
    const CELL = 120;
    const BASE = [
        [1, 2, 3, 4],
        [3, 4, 1, 2],
        [2, 1, 4, 3],
        [4, 3, 2, 1],
    ];

    let core;
    let puzzle = [];
    let solution = [];
    let givenMask = [];
    let sel = -1;
    let filled = 0;
    let errors = 0;
    let blanks = 0;
    const cells = [];

    const rnd = (lo, hi) => Math.floor(Math.random() * (hi - lo + 1)) + lo;

    function shuffle(a) {
        for (let i = a.length - 1; i > 0; i--) {
            const j = rnd(0, i);
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function fits(g, r, c, v) {
        for (let i = 0; i < 4; i++) {
            if (g[r * 4 + i] === v || g[i * 4 + c] === v) return false;
        }
        const br = r - (r % 2);
        const bc = c - (c % 2);
        for (let i = 0; i < 2; i++) {
            for (let j = 0; j < 2; j++) {
                if (g[(br + i) * 4 + bc + j] === v) return false;
            }
        }
        return true;
    }

    function countSolutions(g, limit) {
        let n = 0;
        const walk = () => {
            if (n >= limit) return;
            const i = g.indexOf(0);
            if (i < 0) { n++; return; }
            const r = Math.floor(i / 4);
            const c = i % 4;
            for (let v = 1; v <= 4; v++) {
                if (fits(g, r, c, v)) {
                    g[i] = v;
                    walk();
                    g[i] = 0;
                }
            }
        };
        walk();
        return n;
    }

    function makeSolution() {
        const digits = shuffle([1, 2, 3, 4]);
        const bandsR = shuffle([0, 1]);
        const bandsC = shuffle([0, 1]);
        const rows = bandsR.flatMap((b) => shuffle([b * 2, b * 2 + 1]));
        const cols = bandsC.flatMap((b) => shuffle([b * 2, b * 2 + 1]));
        const out = [];
        for (let r = 0; r < 4; r++) {
            for (let c = 0; c < 4; c++) out.push(digits[BASE[rows[r]][cols[c]] - 1]);
        }
        return out;
    }

    function makePuzzle() {
        solution = makeSolution();
        puzzle = solution.slice();
        const want = core.cfg.blanks;
        let removed = 0;
        for (const i of shuffle([...Array(16).keys()])) {
            if (removed >= want) break;
            const keep = puzzle[i];
            puzzle[i] = 0;
            if (countSolutions(puzzle.slice(), 2) === 1) removed++;
            else puzzle[i] = keep;
        }
        givenMask = puzzle.map((v) => v !== 0);
        blanks = removed;
    }

    function build() {
        boardEl.innerHTML = '';
        cells.length = 0;
        for (let i = 0; i < 16; i++) {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'sd-cell';
            const r = Math.floor(i / 4);
            const c = i % 4;
            if (c === 1) b.classList.add('box-r');
            if (r === 1) b.classList.add('box-b');
            b.addEventListener('click', () => select(i));
            boardEl.appendChild(b);
            cells.push(b);
        }
        padEl.innerHTML = '';
        for (let v = 1; v <= 4; v++) {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'sd-key';
            b.textContent = v;
            b.addEventListener('click', () => place(v));
            padEl.appendChild(b);
        }
    }

    function render() {
        const cur = sel >= 0 ? puzzle[sel] : 0;
        cells.forEach((el, i) => {
            el.textContent = puzzle[i] || '';
            el.classList.toggle('given', givenMask[i]);
            el.classList.toggle('mine', !givenMask[i] && puzzle[i] !== 0);
            el.classList.toggle('sel', i === sel);
            el.classList.toggle('same', cur !== 0 && puzzle[i] === cur && i !== sel);
        });
    }

    function select(i) {
        if (!core.playing()) return;
        sel = i;
        render();
    }

    function nextEmpty(from) {
        for (let k = 1; k <= 16; k++) {
            const i = (from + k) % 16;
            if (puzzle[i] === 0) return i;
        }
        return -1;
    }

    function place(v) {
        if (!core.playing() || sel < 0 || puzzle[sel] !== 0) return;
        const el = cells[sel];
        const x = el.offsetLeft + boardEl.offsetLeft + CELL / 2;
        const y = el.offsetTop + boardEl.offsetTop;
        if (solution[sel] === v) {
            puzzle[sel] = v;
            filled++;
            core.addScore(core.cfg.cellPoints);
            core.popup(x, y, '+' + core.cfg.cellPoints, 'good');
            core.sfx('playCorrectAnswer');
            core.retrigger(mascot, 'happy', 500);
            sel = nextEmpty(sel);
            render();
            if (filled >= blanks) {
                core.addScore(core.cfg.bonusBase + Math.floor(core.timeLeft()) * core.cfg.bonusPerSecond);
                core.finish('win');
            }
        } else {
            errors++;
            core.sfx('playIncorrectAnswer');
            core.retrigger(el, 'wrong', 500);
            core.popup(x, y, 'ops!', 'bad');
        }
    }

    function move(dr, dc) {
        if (!core.playing()) return;
        const base = sel < 0 ? 0 : sel;
        const r = (Math.floor(base / 4) + dr + 4) % 4;
        const c = ((base % 4) + dc + 4) % 4;
        sel = r * 4 + c;
        render();
    }

    window.addEventListener('keydown', (e) => {
        if (!core || !core.playing()) return;
        const m = e.code.match(/^(?:Digit|Numpad)([1-4])$/);
        if (m) { place(parseInt(m[1], 10)); e.preventDefault(); return; }
        if (e.code === 'ArrowUp') { move(-1, 0); e.preventDefault(); }
        else if (e.code === 'ArrowDown') { move(1, 0); e.preventDefault(); }
        else if (e.code === 'ArrowLeft') { move(0, -1); e.preventDefault(); }
        else if (e.code === 'ArrowRight') { move(0, 1); e.preventDefault(); }
    });

    ArcadeCore((api) => {
        core = api;
        return {
            reset() {
                filled = 0;
                errors = 0;
                makePuzzle();
                build();
                sel = puzzle.indexOf(0);
                render();
            },
            summary(reason) {
                if (reason === 'win') {
                    return {
                        title: 'resolvido!',
                        msg: 'sobraram ' + Math.ceil(core.timeLeft()) + ' segundos, que rapidez!',
                        good: true,
                        stats: [
                            { label: 'casinhas', value: filled + '/' + blanks },
                            { label: 'erros', value: errors },
                        ],
                    };
                }
                return {
                    title: 'tempo esgotado!',
                    msg: 'quase lá! faltaram ' + (blanks - filled) + ' casinhas.',
                    good: false,
                    stats: [
                        { label: 'casinhas', value: filled + '/' + blanks },
                        { label: 'erros', value: errors },
                    ],
                };
            },
        };
    });
})();
