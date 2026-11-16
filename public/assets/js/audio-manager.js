
const AUDIO_BASE = document.currentScript.src.replace(/js\/audio-manager\.js.*$/, 'audio/');

const store = {
    get(key) { try { return localStorage.getItem(key); } catch { return null; } },
    set(key, value) { try { localStorage.setItem(key, value); } catch {  } },
};

class SoundManager {
    constructor() {
        this.sounds = {};
        this.lastClick = 0;
        this._groups = [];

        this.volume = {
            music: this._loadVolume('musicVolume', 0.5),
            fx:    this._loadVolume('fxVolume', 0.8),
        };
        this.muted = {
            music: store.get('musicMuted') === 'true',
            fx:    store.get('fxMuted') === 'true',
        };

        this.initAudio();
    }

    _loadVolume(key, fallback) {
        const v = parseFloat(store.get(key));
        return Number.isFinite(v) ? Math.max(0, Math.min(1, v)) : fallback;
    }

    channelOf(key) { return key === 'background' ? 'music' : 'fx'; }

    initAudio() {
        const audioConfig = {
            'background':    { gain: 1,    loop: true },
            'correct':       { gain: 1,    loop: false },
            'incorrect':     { gain: 0.9,  loop: false },
            'levelComplete': { gain: 1,    loop: false },
            'levelUnlock':   { gain: 1,    loop: false },
            'click':         { gain: 0.75, loop: false },
        };

        for (const [key, config] of Object.entries(audioConfig)) {
            const audio = new Audio();
            audio.id = `audio-${key}`;
            audio.loop = config.loop;
            audio.preload = 'auto';
            audio.src = `${AUDIO_BASE}${key}.mp3`;   
            this.sounds[key] = audio;
            this._gain = this._gain || {};
            this._gain[key] = config.gain;
            this._apply(key);
        }

        const bg = this.sounds['background'];
        bg.addEventListener('loadedmetadata', () => {
            const t = parseFloat(sessionStorage.getItem('bgTime'));
            if (t && bg.duration) bg.currentTime = t % bg.duration;
        });
        window.addEventListener('pagehide', () => {
            sessionStorage.setItem('bgTime', bg.currentTime);
        });
    }

    _apply(key) {
        const ch = this.channelOf(key);
        const audio = this.sounds[key];
        audio.volume = Math.max(0, Math.min(1, this.volume[ch] * this._gain[key]));
        audio.muted = this.muted[ch];
    }

    _applyChannel(ch) {
        for (const key of Object.keys(this.sounds)) {
            if (this.channelOf(key) === ch) this._apply(key);
        }
    }

    /* ------------------------------------------------------------ configuração */

    getVolume(ch) { return this.volume[ch]; }
    isChannelMuted(ch) { return this.muted[ch]; }

    /** ch = 'music' | 'fx'; v = 0–1. Salva e aplica na hora. */
    setVolume(ch, v) {
        this.volume[ch] = Math.max(0, Math.min(1, Number(v) || 0));
        store.set(ch === 'music' ? 'musicVolume' : 'fxVolume', this.volume[ch]);
        this._applyChannel(ch);
    }

    setMuted(ch, muted) {
        this.muted[ch] = !!muted;
        store.set(ch === 'music' ? 'musicMuted' : 'fxMuted', this.muted[ch]);
        this._applyChannel(ch);
    }

    toggleMuted(ch) { this.setMuted(ch, !this.muted[ch]); }

    /** true se os efeitos vão ser ouvidos (usado para adiar a navegação só quando há som) */
    get fxAudible() { return !this.muted.fx && this.volume.fx > 0; }

   
    bindControls(root = document) {
        root.querySelectorAll('[data-channel]').forEach((group) => {
            const ch = group.dataset.channel;
            const slider = group.querySelector('[data-volume]');
            const muteBtn = group.querySelector('[data-mute]');
            if (!slider || group._bound) return;
            group._bound = true;

            group._sync = () => {
                slider.value = Math.round(this.volume[ch] * 100);
                slider.style.setProperty('--value', slider.value + '%');
                group.classList.toggle('is-muted', this.muted[ch]);
                muteBtn?.setAttribute('aria-pressed', String(this.muted[ch]));
            };

            slider.addEventListener('input', () => {
                this.setVolume(ch, slider.value / 100);
                if (this.muted[ch] && this.volume[ch] > 0) this.setMuted(ch, false);   // mexeu no volume = quer ouvir
                group._sync();
            });
            // ao soltar o slider dos efeitos, toca um clique para a pessoa ouvir o volume
            if (ch === 'fx') slider.addEventListener('change', () => this.play('click'));

            muteBtn?.addEventListener('click', () => {
                this.toggleMuted(ch);
                group._sync();
            });

            group._sync();
            this._groups.push(group);
        });
    }

    syncControls() { this._groups.forEach((g) => g._sync()); }

    /* ---------------------------------------------------------------- reprodução */

    play(soundKey) {
        const audio = this.sounds[soundKey];
        if (!audio) {
            console.warn(`Som "${soundKey}" não encontrado`);
            return;
        }
        const ch = this.channelOf(soundKey);
        if (this.muted[ch] || this.volume[ch] <= 0) return;

        if (soundKey === 'click') {
            const now = performance.now();
            if (now - this.lastClick < 80) return;
            this.lastClick = now;
        }

        audio.currentTime = 0;
        const promise = audio.play();  
        if (promise !== undefined) {
            promise.catch((error) => console.warn(`Erro ao tocar ${soundKey}:`, error));
        }
    }

    stop(soundKey) {
        const audio = this.sounds[soundKey];
        if (!audio) return;
        audio.pause();
        audio.currentTime = 0;
    }

    startBackground() {
        sessionStorage.setItem('bgOn', '1');
        this.sounds['background'].play().catch(() => {});
    }

    stopBackground() {
        sessionStorage.setItem('bgOn', '0');
        this.sounds['background'].pause();
        this.sounds['background'].currentTime = 0;
    }

    toggleBackground() {
        const bg = this.sounds['background'];
        if (bg.paused) this.startBackground();
        else { bg.pause(); sessionStorage.setItem('bgOn', '0'); }
    }

    playCorrectAnswer()  { this.play('correct'); }
    playIncorrectAnswer() { this.play('incorrect'); }
    playLevelComplete()  { this.play('levelComplete'); }
    playLevelUnlock()    { this.play('levelUnlock'); }
    playClickSound()     { this.play('click'); }
}

const soundManager = new SoundManager();

window.soundManager = soundManager;


(function () {
    const unlock = () => {
        if (sessionStorage.getItem('bgOn') !== '0') soundManager.startBackground();
        document.removeEventListener('pointerdown', unlock);
        document.removeEventListener('keydown', unlock);
    };
    document.addEventListener('pointerdown', unlock);
    document.addEventListener('keydown', unlock);
    if (sessionStorage.getItem('bgOn') === '1') soundManager.startBackground();

  
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href]');
        const btn = e.target.closest('button');
        if (!btn && !link) return;
        if (btn && btn.disabled) return;

        soundManager.playClickSound();

        if (link && !e.defaultPrevented && !e.ctrlKey && !e.metaKey && !e.shiftKey
            && e.button === 0 && link.target !== '_blank' && !link.hasAttribute('download')
            && !link.getAttribute('href').startsWith('#') && soundManager.fxAudible) {
            e.preventDefault();
            setTimeout(() => { window.location.href = link.href; }, 150);
        }
    });
})();
