// /public/assets/js/vn-engine.js
// Engine de visual novel do AniMath.
//
// Uma CENA é só um objeto JSON:
//   { slug: "level_1_intro", title: "...", lines: [ FALA, FALA, ... ] }
//
// Uma FALA pode ter:
//   id       – identificador (gerado automaticamente)
//   speaker  – nome que aparece na caixinha ("" = sem nome)
//   text     – texto do diálogo
//   bg       – (opcional) troca o fundo. "" = mantém o atual
//   chars    – (opcional) comandos de personagens:
//              [{ id:"mel", src:"av1.png", pos:"left|center|right", show:true|false }]
//              campos vazios/ausentes = mantém o que já estava
//   part     – (opcional) 2 = esta fala começa a "parte 2" (explicação). Quem já zerou o jogo
//              reentra nos níveis direto daqui.
//
// VARIÁVEIS (opts.vars) — valem em speaker, text, bg e src:
//   {avatar}              → 1 ou 2 (avatar da conta).   Ex.: "src":"av{avatar}.png"
//   {nome}                → nome da conta
//   {masculino|feminino}  → escolhe pelo gênero do avatar. Ex.: "você está {pronto|pronta}?"
//
// O estado (fundo + personagens) PERSISTE de fala em fala, como numa VN de verdade.

const vnUid = () => 'l' + Math.random().toString(36).slice(2, 8);

class VNEngine {
    /**
     * @param {Object}   opts
     * @param {Element}  opts.root        div onde a engine será montada
     * @param {string}   opts.assetsBase  ex.: '/animath/public/assets/img/vn'
     * @param {string}   opts.sceneUrl    endpoint GET  ?slug=...
     * @param {string}   opts.saveUrl     endpoint POST (só admin)
     * @param {number}   opts.typeSpeed   ms por letra (0 = sem animação)
     * @param {boolean}  opts.overlay     true = cobre a tela toda (modo "pop-up" de qualquer página)
     * @param {boolean}  opts.hideOnEnd   esconde a engine ao terminar (padrão: = overlay)
     * @param {Object}   opts.vars        { avatar: 1|2, gender: 'm'|'f', nome: '...' } (ver VARIÁVEIS acima)
     */
    constructor(opts) {
        this.root = opts.root;
        this.assetsBase = opts.assetsBase;
        this.sceneUrl = opts.sceneUrl;
        this.saveUrl = opts.saveUrl;
        this.overlay = !!opts.overlay;
        this.hideOnEnd = opts.hideOnEnd ?? this.overlay;
        this.vars = { avatar: 1, gender: 'm', nome: '', ...(opts.vars || {}) };

        const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
        this.typeSpeed = reduced ? 0 : (opts.typeSpeed ?? 28);

        this.scene = null;
        this.index = -1;
        this.state = { bg: null, chars: {} };
        this.stack = [];          // cenas "pausadas" (para insertScene)
        this.typing = false;
        this._ended = false;
        this._ev = {};
        this._bgUrl = null;
        this._bgFront = 0;
        this.charEls = new Map();

        this._build();
        if (opts.onEnd) this.on('end', opts.onEnd);

        document.addEventListener('keydown', (e) => {
            if (this.root.hidden || this._ended) return;
            if (document.body.classList.contains('modal-open')) return;   // overlay de configurações aberto
            if (/INPUT|TEXTAREA|SELECT/.test(document.activeElement?.tagName || '')) return;
            if (['Enter', ' ', 'ArrowRight'].includes(e.key)) {
                e.preventDefault();
                this.next();
            }
        });
    }

    /* ------------------------------------------------------------------ DOM */

    _build() {
        this.root.classList.add('vn-root');
        this.root.classList.toggle('vn-overlay', this.overlay);
        this.root.innerHTML = `
            <div class="vn-stage">
                <div class="vn-bg"></div>
                <div class="vn-bg"></div>
                <div class="vn-chars"></div>
                <div class="vn-dialog">
                    <div class="vn-name" hidden></div>
                    <div class="vn-text"></div>
                    <span class="vn-next" aria-hidden="true">▼</span>
                </div>
            </div>`;
        const q = (s) => this.root.querySelector(s);
        this.stage = q('.vn-stage');
        this.bgLayers = [...this.root.querySelectorAll('.vn-bg')];
        this.charLayer = q('.vn-chars');
        this.nameEl = q('.vn-name');
        this.textEl = q('.vn-text');
        this.nextEl = q('.vn-next');

        this.stage.addEventListener('click', () => this.next());
    }

    /* --------------------------------------------------------------- eventos */

    on(evt, fn) { (this._ev[evt] ||= []).push(fn); return this; }
    emit(evt, ...args) { (this._ev[evt] || []).forEach((fn) => fn(...args)); }

    /* ------------------------------------------------------------- variáveis */

    /** Troca {masculino|feminino}, {avatar} e {nome}. O texto original (no JSON) não muda. */
    fmt(str) {
        if (typeof str !== 'string' || !str.includes('{')) return str;
        const v = this.vars;
        return str
            .replace(/\{([^{}|]*)\|([^{}|]*)\}/g, (_, m, f) => (v.gender === 'f' ? f : m))
            .replace(/\{avatar\}/g, String(v.avatar))
            .replace(/\{nome\}/g, v.nome || '');
    }

    /** Índice da 1ª fala marcada com part:2 (ou 0 se a cena não tem parte 2). */
    part2Index(scene = this.scene) {
        const i = (scene?.lines || []).findIndex((l) => Number(l.part) === 2);
        return i < 0 ? 0 : i;
    }

    /* ----------------------------------------------------- carregar / tocar */

    async loadScene(slug) {
        const res = await fetch(`${this.sceneUrl}?slug=${encodeURIComponent(slug)}`);
        if (!res.ok) throw new Error(`Cena "${slug}" não encontrada (${res.status})`);
        return res.json();
    }

    /** Toca uma cena (objeto ou slug) do começo (ou de `at`). Substitui a cena atual. */
    async play(sceneOrSlug, { at = 0 } = {}) {
        const scene = typeof sceneOrSlug === 'string' ? await this.loadScene(sceneOrSlug) : sceneOrSlug;
        scene.lines ||= [];
        scene.lines.forEach((l) => { l.id ||= vnUid(); });

        this.scene = scene;
        this._ended = false;
        this.root.hidden = false;
        this.emit('scene', scene);
        this.goTo(at);
        return scene;
    }

    /**
     * Insere uma sessão de VN A QUALQUER MOMENTO.
     * A cena atual (se houver) é pausada e retomada exatamente de onde parou quando a nova terminar.
     */
    async insertScene(sceneOrSlug) {
        const running = this.scene && !this._ended && !this.root.hidden;
        if (running) {
            this.stack.push({ scene: this.scene, index: this.index, state: this.state });
        }
        try {
            return await this.play(sceneOrSlug);
        } catch (err) {
            if (running) this.stack.pop();
            throw err;
        }
    }

    /* ------------------------------------------------------------ navegação */

    next() {
        if (!this.scene || this._ended) return;
        if (this.typing) return this._skipType();
        if (this.index < this.scene.lines.length - 1) this.goTo(this.index + 1);
        else this._finish();
    }

    /** Encerra a navegação da cena (usado quando o quiz assume a tela). */
    stop() {
        clearTimeout(this._t);
        this.typing = false;
        this._ended = true;
    }

    prev() {
        if (this.scene && this.index > 0) this.goTo(this.index - 1);
    }

    /** Vai direto para a fala `i` (reconstruindo fundo e personagens). */
    goTo(i, { instant = false } = {}) {
        const n = this.scene.lines.length;
        this.index = n ? Math.max(0, Math.min(i, n - 1)) : -1;
        this.state = this._stateUpTo(this.index);
        this._renderState();
        this._renderLine(this.scene.lines[this.index], instant);
        this.emit('line', this.index);
    }

    _finish() {
        const finished = this.scene;
        if (this.stack.length) {
            const s = this.stack.pop();
            this.scene = s.scene;
            this.index = s.index;
            this.state = s.state;
            this._renderState();
            this._renderLine(this.scene.lines[this.index], true);
            this.emit('scene', this.scene);
            this.emit('line', this.index);
            this.emit('end', finished);
            return;
        }
        this._ended = true;
        if (this.hideOnEnd) this.root.hidden = true;
        this.emit('end', finished);
    }

    /* ---------------------------------------------------------------- estado */

    _applyLine(line, st) {
        if (line.bg) st.bg = line.bg;
        for (const c of line.chars || []) {
            if (!c.id) continue;
            const cur = st.chars[c.id] || { id: c.id, src: '', pos: 'center', show: false };
            const patch = {};
            for (const [k, v] of Object.entries(c)) if (v !== undefined && v !== '') patch[k] = v;
            st.chars[c.id] = { ...cur, ...patch };
        }
    }

    _stateUpTo(i) {
        const st = { bg: null, chars: {} };
        for (let k = 0; k <= i; k++) this._applyLine(this.scene.lines[k], st);
        return st;
    }

    _url(kind, src) {
        src = this.fmt(src);
        if (!src) return '';
        if (/^(https?:)?\/\//.test(src) || src.startsWith('/')) return src;
        return `${this.assetsBase}/${kind}/${encodeURIComponent(src)}`;
    }

    _setBg(url) {
        if (url === this._bgUrl) return;
        this._bgUrl = url;
        const front = this.bgLayers[this._bgFront];
        const back = this.bgLayers[1 - this._bgFront];
        back.style.backgroundImage = url ? `url("${url.replace(/"/g, '%22')}")` : 'none';
        back.classList.add('on');
        front.classList.remove('on');
        this._bgFront = 1 - this._bgFront;
    }

    _renderState() {
        this._setBg(this._url('bg', this.state.bg));

        const seen = new Set();
        for (const c of Object.values(this.state.chars)) {
            seen.add(c.id);
            let el = this.charEls.get(c.id);
            if (!el) {
                el = document.createElement('div');
                el.className = 'vn-char';
                el.append(document.createElement('img'));
                this.charLayer.append(el);
                this.charEls.set(c.id, el);
            }
            const img = el.firstChild;
            const url = this._url('chars', c.src);
            if (url && img.getAttribute('src') !== url) img.src = url;
            img.alt = c.id;
            el.dataset.pos = c.pos || 'center';
            el.classList.toggle('vn-visible', !!c.show && !!url);
        }
        for (const [id, el] of this.charEls) if (!seen.has(id)) el.classList.remove('vn-visible');
    }

    /* -------------------------------------------------------- caixa de texto */

    _renderLine(line, instant) {
        clearTimeout(this._t);
        if (!line) {
            this.nameEl.hidden = true;
            this.textEl.textContent = '(cena vazia — abra o editor ✎ para adicionar falas)';
            this.typing = false;
            this.nextEl.classList.remove('on');
            return;
        }
        const speaker = this.fmt(line.speaker || '');
        this.nameEl.hidden = !speaker;
        this.nameEl.textContent = speaker;
        this._type(this.fmt(line.text || ''), instant);
    }

    _type(text, instant) {
        this.typing = false;
        this._chars = Array.from(text);
        this.nextEl.classList.remove('on');
        if (instant || !this.typeSpeed || !this._chars.length) {
            this.textEl.textContent = text;
            this.nextEl.classList.add('on');
            return;
        }
        this.typing = true;
        let n = 0;
        const tick = () => {
            n++;
            this.textEl.textContent = this._chars.slice(0, n).join('');
            if (n >= this._chars.length) return this._skipType();
            this._t = setTimeout(tick, this.typeSpeed);
        };
        tick();
    }

    _skipType() {
        clearTimeout(this._t);
        this.typing = false;
        this.textEl.textContent = this._chars.join('');
        this.nextEl.classList.add('on');
    }

    /* ------------------------------------------- edição em tempo de execução */
    // Tudo abaixo mexe no JSON da cena e atualiza a tela na hora.

    _changed() { this.emit('change', this.scene); }

    /** Redesenha a fala atual sem reiniciar a animação de digitação. */
    refresh() {
        if (!this.scene) return;
        const n = this.scene.lines.length;
        this.index = n ? Math.max(0, Math.min(this.index < 0 ? 0 : this.index, n - 1)) : -1;
        this.state = this._stateUpTo(this.index);
        this._renderState();
        this._renderLine(this.scene.lines[this.index], true);
    }

    addLine(data = {}, at = this.scene.lines.length) {
        const line = { id: vnUid(), speaker: '', text: '', ...data };
        this.scene.lines.splice(at, 0, line);
        if (this.index >= 0 && at <= this.index) this.index++;
        if (this.index < 0) this.index = 0;
        this._changed();
        this.refresh();
        return line;
    }

    editLine(i, patch) {
        Object.assign(this.scene.lines[i], patch);
        this._changed();
        this.refresh();
    }

    removeLine(i) {
        this.scene.lines.splice(i, 1);
        if (i < this.index) this.index--;
        this._changed();
        this.refresh();
    }

    moveLine(i, dir) {
        const j = i + dir;
        const L = this.scene.lines;
        if (j < 0 || j >= L.length) return;
        [L[i], L[j]] = [L[j], L[i]];
        this._changed();
        this.refresh();
    }

    // Atalhos: gravam o comando na fala ATUAL (então ficam salvos no JSON).
    setBackground(src) { this.editLine(this.index, { bg: src }); }
    showCharacter(id, src, pos = 'center') {
        const line = this.scene.lines[this.index];
        const chars = (line.chars || []).filter((c) => c.id !== id);
        chars.push({ id, src, pos, show: true });
        this.editLine(this.index, { chars });
    }
    hideCharacter(id) {
        const line = this.scene.lines[this.index];
        const chars = (line.chars || []).filter((c) => c.id !== id);
        chars.push({ id, show: false });
        this.editLine(this.index, { chars });
    }

    /* ---------------------------------------------------------------- salvar */

    toJSON() {
        const { slug, level_id = null, title, lines } = this.scene;
        return { slug, level_id, title: title || slug, lines };
    }

    async save() {
        const res = await fetch(this.saveUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.toJSON()),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.error || `Erro ${res.status}`);
        return data;
    }
}

window.VNEngine = VNEngine;
