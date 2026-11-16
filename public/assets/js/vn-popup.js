
(() => {
    const SIGN = { '+': '+', '-': '−', '*': '×', '/': '÷' };
    const digits = (n) => String(n).split('').map(Number);
    const el = (tag, cls, text) => {
        const e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text !== undefined) e.textContent = text;
        return e;
    };


    function addModel(a, b) {
        const A = digits(a), B = digits(b), S = digits(a + b);
        const cols = Math.max(S.length, A.length, B.length) + 1;      // coluna 0 = sinal
        const cells = [], active = {};
        const put = (r, c, text, o = {}) => cells.push({ r, c, text, step: 0, ...o });

        A.forEach((d, i) => put(1, cols - A.length + i, d));
        B.forEach((d, i) => put(2, cols - B.length + i, d, { cls: 'ln' }));
        put(2, 0, '+', { cls: 'op' });

        let carry = 0;
        for (let k = 0; k < S.length; k++) {
            const c = cols - 1 - k;
            const t = (A[A.length - 1 - k] ?? 0) + (B[B.length - 1 - k] ?? 0) + carry;
            carry = Math.floor(t / 10);
            put(3, c, t % 10, { step: k + 1, cls: 'res' });
            if (carry && k < S.length - 1) put(0, c - 1, carry, { step: k + 1, cls: 'carry' });
            active[k + 1] = c;
        }
        return { rows: 4, cols, cells, active, maxStep: S.length };
    }

    function subModel(a, b) {                                          // exige a >= b
        const A = digits(a), B = digits(b);
        const n = A.length, cols = n + 1;
        const t = A.slice().reverse(), bb = B.slice().reverse();
        const R = digits(a - b);
        const cells = [], active = {};
        const put = (r, c, text, o = {}) => { const x = { r, c, text, step: 0, ...o }; cells.push(x); return x; };

        const aCell = [];
        A.forEach((d, i) => { aCell[n - 1 - i] = put(1, 1 + i, d); });   // aCell[k] = dígito da coluna k (k=0 unidades)
        B.forEach((d, i) => put(2, cols - B.length + i, d, { cls: 'ln' }));
        put(2, 0, '−', { cls: 'op' });

        const top = [];
        const setTop = (m, val, step, cls) => {
            if (top[m]) top[m].until = step;                          // o valor novo substitui o anterior
            top[m] = put(0, cols - 1 - m, val, { step, cls: 'carry ' + (cls || '') });
            if (aCell[m].strike === undefined) aCell[m].strike = step;
        };

        let last = 0;
        for (let k = 0; k < n; k++) {
            const step = k + 1;
            active[step] = cols - 1 - k;
            if (t[k] < (bb[k] ?? 0)) {                                  // precisa "emprestar"
                let j = k + 1;
                while (j < n && t[j] === 0) j++;
                for (let m = k + 1; m < j; m++) { t[m] = 9; setTop(m, 9, step); }
                t[j] -= 1; setTop(j, t[j], step);
                t[k] += 10; setTop(k, t[k], step, 'borrow');
                last = step;
            }
            if (k < R.length) {
                put(3, cols - 1 - k, t[k] - (bb[k] ?? 0), { step, cls: 'res' });
                last = step;
            }
        }
        return { rows: 4, cols, cells, active, maxStep: last };
    }

    function mulModel(a, b) {
        const A = digits(a), B = digits(b), P = digits(a * b);
        const cols = P.length + 1;
        const cells = [], active = {};
        const put = (r, c, text, o = {}) => cells.push({ r, c, text, step: 0, ...o });

        A.forEach((d, i) => put(1, cols - A.length + i, d));
        B.forEach((d, i) => put(2, cols - B.length + i, d, { cls: 'ln' }));
        put(2, 0, '×', { cls: 'op' });

        if (B.length === 1) {                                          
            let carry = 0;
            for (let k = 0; k < A.length; k++) {
                const c = cols - 1 - k;
                const t = A[A.length - 1 - k] * b + carry;
                carry = Math.floor(t / 10);
                put(3, c, t % 10, { step: k + 1, cls: 'res' });
                active[k + 1] = c;
                if (k === A.length - 1) {
                    if (carry) put(3, c - 1, carry, { step: k + 1, cls: 'res' });
                } else if (carry) {
                    put(0, c - 1, carry, { step: k + 1, cls: 'carry' });
                }
            }
            return { rows: 4, cols, cells, active, maxStep: A.length };
        }

        const m = B.length;
        for (let j = 0; j < m; j++) {
            const part = digits(a * B[m - 1 - j]);
            part.forEach((d, i) => put(3 + j, cols - j - part.length + i, d, { step: j + 1, cls: 'res' }));
            if (j === m - 1) put(3 + j, 0, '+', { step: j + 1, cls: 'op' });
        }
        P.forEach((d, i) => put(3 + m, cols - P.length + i, d, { step: m + 1, cls: 'res tot' }));
        return { rows: 4 + m, cols, cells, active, maxStep: m + 1 };
    }

    function divModel(a, d) {
        const A = digits(a), D = digits(d), n = A.length;
        let s = D.length - 1;
        if (s > n - 1) s = n - 1;
        if (Number(A.slice(0, s + 1).join('')) < d && s < n - 1) s++;

        const steps = [];
        let rem = Number(A.slice(0, s + 1).join(''));
        for (let i = s; i < n; i++) {
            const working = i === s ? rem : rem * 10 + A[i];
            const q = Math.floor(working / d), prod = q * d;
            rem = working - prod;
            steps.push({ i, q, prod, rem });
        }
        const Q = steps.length, w = Math.max(D.length, Q);
        const cols = 1 + n + w;                                        
        const rows = 1 + 2 * Q + 1;
        const cells = [], active = {};
        const put = (r, c, text, o = {}) => cells.push({ r, c, text, step: 0, ...o });

        A.forEach((x, i) => put(0, 1 + i, x));
        D.forEach((x, i) => put(0, 1 + n + i, x, { cls: 'dv' + (i === 0 ? ' dv0' : '') }));

        steps.forEach((st, u) => {
            const step = u + 1;
            put(1, 1 + n + u, st.q, { step, cls: 'res qt' });            
            const endCol = 1 + st.i;                                    
            const prodRow = 1 + 2 * u, remRow = 2 + 2 * u;
            pd.forEach((x, i) => put(prodRow, endCol - pd.length + 1 + i, x, { step, cls: 'pr' }));
            put(prodRow, endCol - pd.length, '−', { step, cls: 'op' });
            digits(st.rem).forEach((x, i, arr) => put(remRow, endCol - arr.length + 1 + i, x, { step, cls: 'res' }));
            if (st.i < n - 1) put(remRow, endCol + 1, A[st.i + 1], { step, cls: 'down' });
            active[step] = endCol;
        });
        return { rows, cols, cells, active, maxStep: Q, div: { n, steps } };
    }

    function renderGrid(model, step, prevStep, box) {
        const grid = el('div', 'pop-grid');
        const cw = Math.min(5.4, 38 / model.cols), ch = Math.min(5.8, 27 / model.rows);
        grid.style.setProperty('--cw', cw + 'cqw');
        grid.style.setProperty('--ch', ch + 'cqw');
        grid.style.gridTemplateColumns = `repeat(${model.cols}, var(--cw))`;
        grid.style.gridAutoRows = 'var(--ch)';

        const ac = step >= 1 ? model.active?.[step] : undefined;
        if (ac !== undefined) {
            const hl = el('div', 'pop-active');
            hl.style.gridColumn = String(ac + 1);
            hl.style.gridRow = `1 / ${model.rows + 1}`;
            grid.append(hl);
        }
        for (const c of model.cells) {
            if (c.step > step) continue;
            if (c.until !== undefined && step >= c.until) continue;
            const e = el('div', 'pop-cell ' + (c.cls || ''), String(c.text));
            e.style.gridRow = String(c.r + 1);
            e.style.gridColumn = String(c.c + 1);
            if (c.strike !== undefined && step >= c.strike) e.classList.add('struck');
            if (prevStep !== null && c.step > prevStep && c.step <= step) e.classList.add('pop-new');
            if (c.strike !== undefined && prevStep !== null && c.strike > prevStep && c.strike <= step) e.classList.add('pop-new-strike');
            grid.append(e);
        }
        box.append(grid);
    }

    function eq(parts, step, prevStep) {                               
        const row = el('div', 'pop-eq');
        for (const p of parts) {
            if (p.step > step) continue;
            const s = el('span', p.cls || '', p.t);
            if (prevStep !== null && p.step > prevStep) s.classList.add('pop-new');
            row.append(s);
        }
        return row;
    }

    function renderItems(p, step, prevStep, box, ctx) {
        const icon = (extra = '') => {
            if (p.src) {
                const i = el('img', 'pop-icon ' + extra);
                i.src = ctx.url('chars', p.src); i.alt = '';
                return i;
            }
            return el('span', 'pop-icon ' + extra, p.icon || '●');
        };
        const fresh = (node, st) => { if (prevStep !== null && st > prevStep) node.classList.add('pop-new'); return node; };
        const cnt = (n, extra) => { const f = document.createDocumentFragment(); for (let i = 0; i < n; i++) f.append(icon(extra)); return f; };
        const op = p.op || '+';
        const area = el('div', 'pop-items');
        let maxStep = 0, eqs = [];

        if (op === '+') {
            const g = p.groups || [2, 3], sum = g.reduce((x, y) => x + y, 0);
            maxStep = g.length + 1;
            g.forEach((n, i) => {
                if (i + 1 > step) return;
                if (i > 0) area.append(fresh(el('span', 'pop-sign', '+'), i + 1));
                const grp = el('div', 'pop-group'); grp.append(cnt(n));
                area.append(fresh(grp, i + 1));
            });
            const parts = [];
            g.forEach((n, i) => { if (i > 0) parts.push({ t: '+', step: i + 1 }); parts.push({ t: String(n), step: i + 1 }); });
            parts.push({ t: '=', step: g.length + 1 }, { t: String(sum), step: g.length + 1, cls: 'res' });
            eqs.push(parts);
            if (step >= g.length + 1) { area.append(fresh(el('span', 'pop-sign', '='), g.length + 1)); area.append(fresh(el('span', 'pop-total', String(sum)), g.length + 1)); }
        } else if (op === '-') {
            const T = p.total ?? 5, r = p.remove ?? 2;
            maxStep = 3;
            const grp = el('div', 'pop-group wide');
            for (let i = 0; i < T; i++) {
                const ic = icon(i >= T - r && step >= 2 ? 'gone' : '');
                if (i >= T - r && step === 2 && prevStep !== null && prevStep < 2) ic.classList.add('pop-new-strike');
                grp.append(ic);
            }
            area.append(fresh(grp, 1));
            eqs.push([{ t: String(T), step: 1 }, { t: '−', step: 2 }, { t: String(r), step: 2 }, { t: '=', step: 3 }, { t: String(T - r), step: 3, cls: 'res' }]);
            if (step >= 3) area.append(fresh(el('span', 'pop-total', '= ' + (T - r)), 3));
        } else if (op === '*') {
            const n = p.times ?? (p.groups ? p.groups.length : 3), per = p.per ?? (p.groups ? p.groups[0] : 2);
            const prod = n * per;
            maxStep = n + 2;
            for (let i = 0; i < n; i++) {
                if (i + 1 > step) break;
                const grp = el('div', 'pop-group basket'); grp.append(cnt(per));
                area.append(fresh(grp, i + 1));
            }
            const sumParts = [];
            for (let i = 0; i < n; i++) { if (i) sumParts.push({ t: '+', step: n + 1 }); sumParts.push({ t: String(per), step: n + 1 }); }
            sumParts.push({ t: '=', step: n + 1 }, { t: String(prod), step: n + 1, cls: 'res' });
            eqs.push(sumParts);
            eqs.push([{ t: String(n), step: n + 2 }, { t: '×', step: n + 2 }, { t: String(per), step: n + 2 }, { t: '=', step: n + 2 }, { t: String(prod), step: n + 2, cls: 'res' }]);
        } else {                                                        // '/'
            const T = p.total ?? 6, P = p.parts ?? 3, q = Math.floor(T / P), left = T - q * P;
            maxStep = 4;
            if (step <= 2) { const pile = el('div', 'pop-group wide'); pile.append(cnt(T)); area.append(fresh(pile, 1)); }
            if (step >= 2) {
                const plates = el('div', 'pop-plates');
                for (let i = 0; i < P; i++) {
                    const pl = el('div', 'pop-plate');
                    if (step >= 3) pl.append(cnt(q));
                    plates.append(pl);
                }
                fresh(plates, step >= 3 ? 3 : 2);
                area.append(plates);
                if (step >= 3 && left) { const lf = el('div', 'pop-group'); lf.append(cnt(left)); area.append(lf); }
            }
            eqs.push([{ t: String(T), step: 4 }, { t: '÷', step: 4 }, { t: String(P), step: 4 }, { t: '=', step: 4 }, { t: String(q), step: 4, cls: 'res' }]);
        }
        box.append(area);
        for (const parts of eqs) { const row = eq(parts, step, prevStep); if (row.childNodes.length) box.append(row); }
        return maxStep;
    }

    function renderPopup(box, p, ctx = {}) {
        const url = ctx.url || ((k, s) => s);
        const prevStep = ctx.prevStep ?? null;
        box.textContent = '';
        let maxStep = 0, step = 0;

        if (p.title) box.append(el('div', 'pop-title', p.title));
        const body = el('div', 'pop-body');
        box.append(body);

        if (p.type === 'items') {
            const probe = el('div');
            const mx = renderItems(p, 99, null, probe, { url });
            step = Math.min(p.step ?? mx, mx);
            renderItems(p, step, prevStep, body, { url });
            maxStep = mx;
        } else if (p.type === 'column') {
            const op = p.op || '+', a = Math.max(0, Math.floor(p.a || 0)), b = Math.max(0, Math.floor(p.b || 0));
            let m;
            if (op === '+') m = addModel(a, b);
            else if (op === '-') m = a >= b ? subModel(a, b) : null;
            else if (op === '*') m = mulModel(a, Math.max(1, b));
            else m = b > 0 ? divModel(a, b) : null;
            if (!m) { body.append(el('div', 'pop-eq', '?')); }
            else {
                maxStep = m.maxStep;
                step = Math.min(p.step ?? m.maxStep, m.maxStep);
                renderGrid(m, step, prevStep, body);
            }
        } else if (p.type === 'equation') {
            const e = el('div', 'pop-eq big');
            const text = String(p.text || '');
            const mark = p.mark && text.includes(p.mark) ? p.mark : null;
            if (mark) {
                const i = text.indexOf(mark);
                e.append(el('span', '', text.slice(0, i)), el('span', 'mark', mark), el('span', '', text.slice(i + mark.length)));
            } else e.textContent = text;
            body.append(e);
        } else if (p.type === 'image') {
            const img = el('img', 'pop-img'); img.src = url('popup', p.src || ''); img.alt = '';
            body.append(img);
        }
        if (p.caption) box.append(el('div', 'pop-caption', p.caption));
        return { maxStep, step };
    }

    window.VNPopup = { render: renderPopup, models: { add: addModel, sub: subModel, mul: mulModel, div: divModel } };
})();
