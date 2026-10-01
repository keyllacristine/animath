// /public/assets/js/audio-manager.js

class SoundManager {
    constructor() {
        this.sounds = {};
        this.isMuted = localStorage.getItem('soundsMuted') === 'true' || false;
        this.masterVolume = parseFloat(localStorage.getItem('masterVolume')) || 1;
        this.initAudio();
    }

    initAudio() {
        // Criar elementos de áudio dinamicamente
        const audioConfig = {
            'background': { volume: 0.5, loop: true },
            'correct': { volume: 0.8, loop: false },
            'incorrect': { volume: 0.7, loop: false },
            'levelComplete': { volume: 0.9, loop: false },
            'levelUnlock': { volume: 0.8, loop: false },
            'click': { volume: 0.6, loop: false }
        };

        for (const [key, config] of Object.entries(audioConfig)) {
            const audio = new Audio();
            audio.id = `audio-${key}`;
            audio.loop = config.loop;
            audio.volume = config.volume * this.masterVolume;
            
            // Determinar o caminho do arquivo (você precisa criar os áudios)
            audio.src = `/assets/audio/${key}.mp3`;
            
            document.body.appendChild(audio);
            this.sounds[key] = audio;
        }
    }

    play(soundKey) {
        if (this.isMuted) return;
        
        const audio = this.sounds[soundKey];
        if (!audio) {
            console.warn(`Som "${soundKey}" não encontrado`);
            return;
        }

        // Reiniciar do início se já está tocando
        audio.currentTime = 0;
        
        // Tentar tocar (alguns navegadores exigem interação do usuário primeiro)
        const promise = audio.play();
        if (promise !== undefined) {
            promise
                .catch(error => console.warn(`Erro ao tocar ${soundKey}:`, error));
        }
    }

    stop(soundKey) {
        const audio = this.sounds[soundKey];
        if (!audio) return;
        
        audio.pause();
        audio.currentTime = 0;
    }

    toggleBackground() {
        const bg = this.sounds['background'];
        if (!bg) return;

        if (bg.paused) {
            bg.play().catch(() => {});
        } else {
            bg.pause();
        }
    }

    setVolume(soundKey, volume) {
        const audio = this.sounds[soundKey];
        if (!audio) return;
        
        audio.volume = Math.max(0, Math.min(1, volume)) * this.masterVolume;
    }

    setMasterVolume(volume) {
        this.masterVolume = Math.max(0, Math.min(1, volume));
        localStorage.setItem('masterVolume', this.masterVolume);
        
        // Atualizar volume de todos os sons
        for (const audio of Object.values(this.sounds)) {
            audio.volume *= this.masterVolume;
        }
    }

    toggleMute() {
        this.isMuted = !this.isMuted;
        localStorage.setItem('soundsMuted', this.isMuted);
        
        for (const audio of Object.values(this.sounds)) {
            audio.muted = this.isMuted;
        }
    }

    startBackground() {
        if (!this.isMuted) {
            this.sounds['background'].play().catch(() => {});
        }
    }

    stopBackground() {
        this.sounds['background'].pause();
        this.sounds['background'].currentTime = 0;
    }

    playCorrectAnswer() {
        this.play('correct');
    }

    playIncorrectAnswer() {
        this.play('incorrect');
    }

    playLevelComplete() {
        this.play('levelComplete');
    }

    playLevelUnlock() {
        this.play('levelUnlock');
    }

    playClickSound() {
        this.play('click');
    }
}

// Inicializar globalmente
const soundManager = new SoundManager();