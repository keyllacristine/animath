

const vnUid = () => 'l' + Math.random().toString(36).slice(2, 8);

class VNEngine {
    /**
     * @param {Object}   opts
     * @param {Element}  opts.root        
     * @param {string}   opts.assetsBase  
     * @param {string}   opts.sceneUrl   
     * @param {number}   opts.typeSpeed   
     * @param {boolean}  opts.overlay    
     * @param {boolean}  opts.hideOnEnd   
     * @param {Object}   opts.vars        
     */
    constructor(opts) {
        this.root = opts.root;
        this.assetsBase = opts.assetsBase;
        this.sceneUrl = opts.sceneUrl;
        this.overlay = !!opts.overlay;
        this.hideOnEnd = opts.hideOnEnd ?? this.overlay;
        this.vars = { avatar: 1, gender: 'm', nome: '', ...(opts.vars || {}) };

        const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
        this.typeSpeed = reduced ? 0 : (opts.typeSpeed ?? 28);

        this.scene = null;
        this.index = -1;
        this.state = { bg: null, chars: {}, popup: null };
        this.stack = [];          
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
            if (document.body.classList.contains('modal-open')) return;   
            if (/INPUT|TEXTAREA|SELECT/.test(document.activeElement?.tagName || '')) return;
            if (['Enter', ' ', 'ArrowRight'].includes(e.key)) {
                e.preventDefault();
                this.next();
            }
        });
    }


    _build() {
        this.root.classList.add('vn-root');
        this.root.classList.toggle('vn-overlay', this.overlay);
        this.root.innerHTML = `
            <div class="vn-stage">
                <div class="vn-bg"></div>
                <div class="vn-bg"></div>
                <div class="vn-chars"></div>
                <div class="vn-popup"></div>
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
        this.popupEl = q('.vn-popup');
        this.nameEl = q('.vn-name');
        this.textEl = q('.vn-text');
        this.nextEl = q('.vn-next');

        this.stage.addEventListener('click', () => this.next());
    }


    on(evt, fn) { (this._ev[evt] ||= []).push(fn); return this; }
    emit(evt, ...args) { (this._ev[evt] || []).forEach((fn) => fn(...args)); }


    fmt(str) {
        if (typeof str !== 'string' || !str.includes('{')) return str;
        const v = this.vars;
        return str
            .replace(/\{([^{}|]*)\|([^{}|]*)\}/g, (_, m, f) => (v.gender === 'f' ? f : m))
            .replace(/\{avatar\}/g, String(v.avatar))
            .replace(/\{nome\}/g, v.nome || '');
    }

    part2Index(scene = this.scene) {
        const i = (scene?.lines || []).findIndex((l) => Number(l.part) === 2);
        return i < 0 ? 0 : i;
    }


    async loadScene(slug) {
        const res = await fetch(`${this.sceneUrl}?slug=${encodeURIComponent(slug)}`);
        if (!res.ok) throw new Error(`Cena "${slug}" não encontrada (${res.status})`);
        return res.json();
    }

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


    next() {
        if (!this.scene || this._ended) return;
        if (this.typing) return this._skipType();
        if (this.index < this.scene.lines.length - 1) this.goTo(this.index + 1);
        else this._finish();
    }

    stop() {
        clearTimeout(this._t);
        this.typing = false;
        this._ended = true;
    }

    prev() {
        if (this.scene && this.index > 0) this.goTo(this.index - 1);
    }

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


    _applyLine(line, st) {
        if (line.bg) st.bg = line.bg;
        if ('popup' in line) {                       
            const p = line.popup;
            if (!p || p.hide) st.popup = null;
            else if (p.type || !st.popup) st.popup = { ...p };
            else st.popup = { ...st.popup, ...p };
        }
        for (const c of line.chars || []) {
            if (!c.id) continue;
            const cur = st.chars[c.id] || { id: c.id, src: '', pos: 'center', show: false };
            const patch = {};
            for (const [k, v] of Object.entries(c)) if (v !== undefined && v !== '') patch[k] = v;
            st.chars[c.id] = { ...cur, ...patch };
        }
    }

    _stateUpTo(i) {
        const st = { bg: null, chars: {}, popup: null };
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
        this._renderPopup(this.state.popup);

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


    _renderPopup(p) {
        const box = this.popupEl;
        if (!p || !window.VNPopup) {
            box.classList.remove('on');
            this.stage.classList.remove('has-popup');
            this._popKey = null;
            return;
        }
        const f = { ...p };
        for (const k of ['title', 'caption', 'text']) if (typeof f[k] === 'string') f[k] = this.fmt(f[k]);

        const key = JSON.stringify({ ...p, step: 0 });
        const same = key === this._popKey && box.classList.contains('on');
        const info = VNPopup.render(box, f, {
            url: (kind, src) => this._url(kind, src),
            prevStep: same ? this._popStep : 0,         
        });
        this._popKey = key;
        this._popStep = info.step;
        box.classList.add('on');
        this.stage.classList.add('has-popup');           
    }


    _renderLine(line, instant) {
        clearTimeout(this._t);
        if (!line) {
            this.nameEl.hidden = true;
            this.textEl.textContent = '(cena vazia)';
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

}
window.VNEngine = VNEngine;
