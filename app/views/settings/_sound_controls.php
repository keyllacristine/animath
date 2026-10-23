<?php /* Controles de volume/mudo — usados em /settings e no overlay do jogo. Ligados por soundManager.bindControls() */ ?>
<div class="sound-controls">
    <div class="control-group" data-channel="music">
        <button type="button" class="sound-icon sound-toggle" data-mute title="Silenciar / ativar música" aria-label="Silenciar ou ativar a música">
            <i class="bi bi-music-note-beamed"></i>
        </button>
        <input type="range" data-volume min="0" max="100" value="50" class="custom-slider" aria-label="Volume da música">
    </div>

    <div class="control-group" data-channel="fx">
        <button type="button" class="sound-icon sound-toggle" data-mute title="Silenciar / ativar efeitos" aria-label="Silenciar ou ativar os efeitos sonoros">
            <i class="bi bi-volume-up"></i>
        </button>
        <input type="range" data-volume min="0" max="100" value="80" class="custom-slider" aria-label="Volume dos efeitos">
    </div>
</div>
