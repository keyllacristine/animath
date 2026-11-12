// /public/assets/js/vn-editor.js
// Painel lateral para editar a cena que está rodando na VNEngine, em tempo real.
// Só carregue este arquivo para admins (a view já faz isso).

const h = (tag, props = {}, ...kids) => {
    const el = document.createElement(tag);
    for (const [k, v] of Object.entries(props)) {
        if (k === 'class') el.className = v;
        else if (k.startsWith('on')) el.addEventListener(k.slice(2), v);
        else if (k !== 'value') el.setAttribute(k, v);
    }
    kids.flat().filter((c) => c != null && c !== false).forEach((c) => el.append(c));
    if ('value' in props) el.value = props.value; // depois dos <option>
    return el;
};

class VNEditor {
    /**
     * @param {VNEngine} engine
     * @param {Object}   opts  { assetsUrl, uploadUrl }
     */
    constructor(engine, opts) {
        this.engine = engine;
        this.assetsUrl = opts.assetsUrl;
        this.uploadUrl = opts.uploadUrl;
        this.assets = { bg: [], chars: [] };

        this._build();
        engine.on('scene', () => this.renderList());
        engine.on('line', (i) => this._highlight(i));
        engine.on('change', () => this._setStatus('alterações não salvas'));

        this.loadAssets().then(() => this.renderList());
    }

    /* ------------------------------------------------------------------ UI */

    _build() {
        const e = this.engine;

        this.toggleBtn = h('button', { class: 'vn-btn vn-fab', title: 'Editar cena', onclick: () => this.toggle() }, '✎ Editar');

        this.statusEl = h('span', { class: 'vn-status' }, '');
        this.slugEl = h('b', {}, '');
        this.listEl = h('div', { class: 'vn-list' });

        const slugIn = h('input', { class: 'vn-in', placeholder: 'slug da cena (ex.: level_2_intro)' });
        const openBtn = h('button', { class: 'vn-btn', onclick: () => this.openScene(slugIn.value.trim()) }, 'Abrir / criar');

        const kindSel = h('select', { class: 'vn-in' },
            h('option', { value: 'bg' }, 'Fundo'),
            h('option', { value: 'chars' }, 'Personagem (PNG)'));
        const fileIn = h('input', { type: 'file', accept: 'image/png,image/jpeg,image/webp,image/gif', class: 'vn-in' });
        const upBtn = h('button', {
            class: 'vn-btn',
            onclick: async () => {
                if (!fileIn.files[0]) return this._setStatus('escolha um arquivo');
                try {
                    await this._upload(kindSel.value, fileIn.files[0]);
                    fileIn.value = '';
                    this._setStatus('imagem enviada ✔');
                } catch (err) { this._setStatus('erro: ' + err.message); }
            },
        }, 'Enviar');

        this.panel = h('aside', { class: 'vn-editor', hidden: '' },
            h('div', { class: 'vn-ed-head' },
                h('span', {}, 'Cena: ', this.slugEl),
                h('button', { class: 'vn-btn vn-btn-main', onclick: () => this.save() }, 'Salvar'),
                h('button', { class: 'vn-btn', onclick: () => this.toggle() }, '✕')),
            this.statusEl,
            h('div', { class: 'vn-row' }, slugIn, openBtn),
            h('div', { class: 'vn-row' }, kindSel, fileIn, upBtn),
            h('button', { class: 'vn-btn vn-btn-main', onclick: () => { e.addLine({}, e.scene.lines.length); this.renderList(); e.goTo(e.scene.lines.length - 1, { instant: true }); } }, '+ Nova fala'),
            this.listEl);

        document.body.append(this.toggleBtn, this.panel);
    }

    toggle() {
        const open = this.panel.hidden;
        this.panel.hidden = !open;
        document.body.classList.toggle('vn-editing', open);
        this.toggleBtn.hidden = open;
    }

    _setStatus(msg) { this.statusEl.textContent = msg; }

    async loadAssets() {
        try {
            const r = await fetch(this.assetsUrl);
            if (r.ok) this.assets = await r.json();
        } catch { /* sem lista: selects ficam só com o valor atual */ }
    }

    async _upload(kind, file) {
        const fd = new FormData();
        fd.append('kind', kind);
        fd.append('image', file);
        const r = await fetch(this.uploadUrl, { method: 'POST', body: fd });
        const j = await r.json().catch(() => ({}));
        if (!r.ok) throw new Error(j.error || 'falha no upload');
        await this.loadAssets();
        this.renderList();
        return j.name;
    }

    async openScene(slug) {
        if (!/^[a-z0-9_-]{1,80}$/i.test(slug)) return this._setStatus('slug inválido (use letras, números, _ e -)');
        try {
            await this.engine.play(slug);
        } catch {
            // não existe ainda → cria uma cena vazia com esse slug
            await this.engine.play({ slug, title: slug, lines: [] });
            this._setStatus('cena nova — adicione falas e salve');
        }
    }

    async save() {
        try {
            await this.engine.save();
            this._setStatus('salvo ✔ ' + new Date().toLocaleTimeString());
        } catch (err) {
            this._setStatus('erro ao salvar: ' + err.message);
        }
    }

    /* --------------------------------------------------------------- lista */

    renderList() {
        const e = this.engine;
        if (!e.scene) return;
        this.slugEl.textContent = e.scene.slug || '(sem slug)';
        this.listEl.replaceChildren(...e.scene.lines.map((line, i) => this._card(line, i)));
        this._highlight(e.index);
    }

    _highlight(i) {
        [...this.listEl.children].forEach((el, k) => el.classList.toggle('active', k === i));
        this.listEl.children[i]?.scrollIntoView({ block: 'nearest' });
    }

    _select(options, current, emptyLabel, onChange) {
        const list = [...options];
        if (current && !list.includes(current)) list.push(current);
        return h('select', { class: 'vn-in', onchange: (ev) => onChange(ev.target.value), value: current || '' },
            h('option', { value: '' }, emptyLabel),
            list.map((o) => h('option', { value: o }, o)));
    }

    _card(line, i) {
        const e = this.engine;
        const btn = (label, title, fn) => h('button', { class: 'vn-btn vn-mini', title, onclick: fn }, label);
        const saveChars = () => e.editLine(i, { chars: line.chars });

        const charRows = (line.chars || []).map((c, k) => h('div', { class: 'vn-charrow' },
            h('input', { class: 'vn-in', placeholder: 'id (ex.: mel)', value: c.id || '', oninput: (ev) => { c.id = ev.target.value.trim(); saveChars(); } }),
            this._select(this.assets.chars, c.src, '(manter imagem)', (v) => { c.src = v; saveChars(); }),
            h('select', { class: 'vn-in', onchange: (ev) => { c.pos = ev.target.value; saveChars(); }, value: c.pos || '' },
                h('option', { value: '' }, '(manter pos.)'),
                h('option', { value: 'far-left' }, 'extrema esquerda'),
                h('option', { value: 'left' }, 'esquerda'),
                h('option', { value: 'mid-left' }, 'centro-esquerda'),
                h('option', { value: 'center' }, 'centro'),
                h('option', { value: 'mid-right' }, 'centro-direita'),
                h('option', { value: 'right' }, 'direita'),
                h('option', { value: 'far-right' }, 'extrema direita')),
            h('select', { class: 'vn-in', onchange: (ev) => { c.show = ev.target.value === '1'; saveChars(); }, value: c.show === false ? '0' : '1' },
                h('option', { value: '1' }, 'mostrar'),
                h('option', { value: '0' }, 'esconder')),
            btn('✕', 'Remover comando', () => { line.chars.splice(k, 1); saveChars(); this.renderList(); })));

        // marca esta fala como início da parte 2 (a reentrada rápida começa daqui)
        const partBox = h('input', { type: 'checkbox', onchange: (ev) => e.editLine(i, { part: ev.target.checked ? 2 : undefined }) });
        partBox.checked = Number(line.part) === 2;

        // pop-up didático: JSON editável + exemplos prontos
        const popupArea = h('textarea', { class: 'vn-in', rows: '4', spellcheck: 'false', placeholder: 'vazio = sem mudança. false = fecha o pop-up' });
        popupArea.value = line.popup === undefined ? '' : JSON.stringify(line.popup);
        const popupMsg = h('div', { class: 'vn-lbl' }, '');
        const applyPopup = (txt) => {
            txt = txt.trim();
            if (txt === '') { e.editLine(i, { popup: undefined }); popupMsg.textContent = ''; return; }
            try { e.editLine(i, { popup: JSON.parse(txt) }); popupMsg.textContent = ''; }
            catch { popupMsg.textContent = 'JSON inválido (ainda não aplicado)'; }
        };
        popupArea.addEventListener('input', () => applyPopup(popupArea.value));
        const presets = {
            '+ objetos':   { type: 'items', op: '+', icon: '🌸', groups: [2, 3], step: 1 },
            '− objetos':   { type: 'items', op: '-', icon: '🌸', total: 5, remove: 2, step: 1 },
            '× objetos':   { type: 'items', op: '*', icon: '🍄', times: 3, per: 2, step: 1 },
            '÷ objetos':   { type: 'items', op: '/', icon: '🐟', total: 6, parts: 3, step: 1 },
            'sinal em destaque': { type: 'equation', text: '3 × 2 = 6', mark: '×', caption: 'sinal da multiplicação' },
            'armação  +':  { type: 'column', op: '+', a: 47, b: 38, step: 0 },
            'armação  −':  { type: 'column', op: '-', a: 52, b: 27, step: 0 },
            'armação  ×':  { type: 'column', op: '*', a: 24, b: 3, step: 0 },
            'armação  ÷':  { type: 'column', op: '/', a: 78, b: 3, step: 0 },
            'avançar passo': { step: 2 },
            'fechar':      false,
        };
        const presetSel = h('select', { class: 'vn-in', onchange: (ev) => {
            const k = ev.target.value; if (!k) return;
            popupArea.value = JSON.stringify(presets[k]); applyPopup(popupArea.value); ev.target.value = '';
        } }, h('option', { value: '' }, '(inserir exemplo…)'), Object.keys(presets).map((k) => h('option', { value: k }, k)));

        const popupDet = h('details', {},
            h('summary', {}, 'Pop-up didático (passos / armação)'),
            presetSel, popupArea, popupMsg);
        popupDet.open = line.popup !== undefined;   // abre sozinho nas falas que já têm pop-up

        return h('div', { class: 'vn-card', onclick: () => { if (e.index !== i) e.goTo(i, { instant: true }); } },
            h('div', { class: 'vn-card-head' },
                h('b', {}, '#' + (i + 1)),
                btn('▶', 'Ir para esta fala', () => e.goTo(i)),
                btn('↑', 'Subir', () => { e.moveLine(i, -1); this.renderList(); }),
                btn('↓', 'Descer', () => { e.moveLine(i, 1); this.renderList(); }),
                btn('＋', 'Inserir fala abaixo', () => { e.addLine({}, i + 1); this.renderList(); }),
                btn('✕', 'Excluir fala', () => { if (confirm('Excluir esta fala?')) { e.removeLine(i); this.renderList(); } })),
            h('input', { class: 'vn-in', placeholder: 'Quem fala (ex.: Mel)', value: line.speaker || '', oninput: (ev) => e.editLine(i, { speaker: ev.target.value }) }),
            h('textarea', { class: 'vn-in', rows: '3', placeholder: 'Texto do diálogo…', value: line.text || '', oninput: (ev) => e.editLine(i, { text: ev.target.value }) }),
            h('label', { class: 'vn-lbl' }, partBox, ' começa a parte 2 (explicação)'),
            popupDet,
            h('details', {},
                h('summary', {}, 'Fundo e personagens'),
                h('label', { class: 'vn-lbl' }, 'Fundo desta fala'),
                this._select(this.assets.bg, line.bg, '(manter fundo atual)', (v) => e.editLine(i, { bg: v })),
                h('label', { class: 'vn-lbl' }, 'Personagens'),
                charRows,
                btn('+ personagem', 'Adicionar comando de personagem', () => {
                    (line.chars ||= []).push({ id: '', src: '', pos: 'center', show: true });
                    this.renderList();
                })));
    }
}

window.VNEditor = VNEditor;
