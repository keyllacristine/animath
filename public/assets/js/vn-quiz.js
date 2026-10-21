// /public/assets/js/vn-quiz.js
// Quiz de contas que roda por cima do fundo onde a visual novel terminou.
//
// Regras:
//  - acerto de PRIMEIRA = 1 acerto (hits)
//  - cada clique errado = 1 erro (errors); a criança tenta de novo a mesma conta
//  - a conta só passa quando acerta; ao completar `count` contas o nível termina

class VNQuiz {
    /**
     * @param {Object}   o
     * @param {Element}  o.stage       vn.stage (a "tela" 16:9 da engine)
     * @param {string}   o.assetsBase  ex.: '/animath/public/assets/img/vn'
     * @param {Object}   o.config      { op:'+|-|*|/', count, min, max, mascot }
     * @param {string}   o.backUrl     para onde voltar ao terminar (tela de níveis)
     * @param {Function} o.onFinish    async ({hits, errors}) => salva o progresso (lance erro se falhar)
     */
    constructor({ stage, assetsBase, config, backUrl, onFinish }) {
        this.stage = stage;
        this.assetsBase = assetsBase;
        this.cfg = { op: '+', count: 10, min: 1, max: 10, mascot: '', ...config };
        this.backUrl = backUrl;
        this.onFinish = onFinish;

        this.hits = 0;
        this.errors = 0;
        this.done = 0;
        this.locked = false;
        this.firstTry = true;
        this._last = '';
    }

    static SYMBOL = { '+': '+', '-': '−', '*': '×', '/': '÷' };
    static PRAISE = ['certíssimo!', 'muito bem!', 'isso aí!', 'acertou!'];

    /* ------------------------------------------------------------------ UI */

    start() {
        this.stage.classList.add('vn-quiz-mode');

        const layer = document.createElement('div');
        layer.className = 'vq-layer';
        layer.innerHTML = `
            <div class="vq-shade"></div>
            <a class="vq-back" title="Voltar"><i class="bi bi-reply-fill"></i></a>
            <div class="vq-card">
                <div class="vq-question"></div>
                <div class="vq-options"></div>
            </div>
            <div class="vq-mascot">
                <div class="vq-bubble"></div>
                <img alt="">
            </div>`;
        this.stage.append(layer);

        this.layer = layer;
        this.card = layer.querySelector('.vq-card');
        this.qEl = layer.querySelector('.vq-question');
        this.optsEl = layer.querySelector('.vq-options');
        this.bubble = layer.querySelector('.vq-bubble');

        layer.querySelector('.vq-back').href = this.backUrl;

        const img = layer.querySelector('.vq-mascot img');
        if (this.cfg.mascot) img.src = `${this.assetsBase}/chars/${encodeURIComponent(this.cfg.mascot)}`;
        else img.hidden = true;

        this._next();
    }

    _next() {
        if (this.done >= this.cfg.count) return this._finish();

        this.q = this._makeQuestion();
        this.firstTry = true;
        this.locked = false;
        this.qEl.textContent = this.q.text;
        this.optsEl.replaceChildren(...this.q.options.map((value) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'vq-opt';
            b.textContent = value;
            b.addEventListener('click', () => this._answer(b, value));
            return b;
        }));
    }

    _answer(btn, value) {
        if (this.locked) return;
        this.locked = true;

        const ok = value === this.q.answer;
        if (ok) {
            if (this.firstTry) this.hits++;
            this.done++;
            btn.classList.add('right');
            this.bubble.textContent = VNQuiz.PRAISE[Math.floor(Math.random() * VNQuiz.PRAISE.length)];
        } else {
            this.errors++;
            this.firstTry = false;
            btn.classList.add('wrong');
            btn.disabled = true;
            this.bubble.textContent = 'oops..!\ntente de novo!';
        }

        try {
            if (ok) window.soundManager?.playCorrectAnswer();
            else window.soundManager?.playIncorrectAnswer();
        } catch { /* som é opcional */ }

        this.layer.classList.add('feedback');
        setTimeout(() => {
            this.layer.classList.remove('feedback');
            if (ok) this._next();
            else this.locked = false;
        }, ok ? 1100 : 1400);
    }

    /* ------------------------------------------------------------- conclusão */

    _finish() {
        this.card.innerHTML = `
            <div class="vq-title">nível concluído!</div>
            <div class="vq-stats">
                <div class="vq-stat hits"><i class="bi bi-check-lg"></i> <span class="n-hits"></span> acertos</div>
                <div class="vq-stat errors"><i class="bi bi-x-lg"></i> <span class="n-errors"></span> erros</div>
            </div>
            <div class="vq-status"></div>
            <button type="button" class="vq-opt vq-go"></button>`;

        this.card.querySelector('.n-hits').textContent = this.hits;
        this.card.querySelector('.n-errors').textContent = this.errors;

        const status = this.card.querySelector('.vq-status');
        const go = this.card.querySelector('.vq-go');

        const save = async () => {
            go.disabled = true;
            go.textContent = '...';
            status.textContent = 'salvando seu progresso…';
            try {
                await this.onFinish({ hits: this.hits, errors: this.errors });
                status.textContent = 'progresso salvo!';
                go.textContent = 'voltar aos níveis';
                go.onclick = () => { window.location.href = this.backUrl; };
            } catch {
                status.textContent = 'não consegui salvar. tente de novo!';
                go.textContent = 'tentar de novo';
                go.onclick = save;
            }
            go.disabled = false;
        };
        save();
    }

    /* --------------------------------------------------------------- contas */

    _makeQuestion() {
        const { min, max } = this.cfg;
        const op = ['+', '-', '*', '/'].includes(this.cfg.op) ? this.cfg.op : '+';
        const rnd = (lo, hi) => Math.floor(Math.random() * (hi - lo + 1)) + lo;
        const lo = Math.max(1, min);
        const hi = Math.max(lo, max);

        let a, b, answer, tries = 0;
        do {
            if (op === '+') { a = rnd(min, max); b = rnd(min, max); answer = a + b; }
            else if (op === '-') { a = rnd(min, max); b = rnd(min, max); if (b > a) [a, b] = [b, a]; answer = a - b; }
            else if (op === '*') { a = rnd(min, max); b = rnd(min, max); answer = a * b; }
            else { b = rnd(lo, hi); answer = rnd(lo, hi); a = b * answer; }   // divisão sempre exata
        } while (`${a}${op}${b}` === this._last && ++tries < 15);

        this._last = `${a}${op}${b}`;
        return {
            text: `${a} ${VNQuiz.SYMBOL[op]} ${b} = ?`,
            answer,
            options: this._options(answer),
        };
    }

    /** 4 alternativas: a certa + 3 números próximos (como no protótipo: 14, 9, 7, 13). */
    _options(answer) {
        const rnd = (lo, hi) => Math.floor(Math.random() * (hi - lo + 1)) + lo;
        const set = new Set([answer]);
        const spread = Math.max(3, Math.round(answer * 0.25));

        for (let guard = 0; set.size < 4 && guard < 200; guard++) {
            const d = rnd(1, spread);
            const v = answer + (Math.random() < 0.5 ? -d : d);
            if (v >= 0) set.add(v);
        }
        for (let k = 1; set.size < 4; k++) set.add(answer + k);

        const list = [...set];
        for (let i = list.length - 1; i > 0; i--) {   // embaralha
            const j = Math.floor(Math.random() * (i + 1));
            [list[i], list[j]] = [list[j], list[i]];
        }
        return list;
    }
}

window.VNQuiz = VNQuiz;
