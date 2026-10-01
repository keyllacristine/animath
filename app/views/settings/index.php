<?php include __DIR__ . '/../header.php'; ?>

<main class="settings-page">
    <div class="top-bar">
        <a href="<?= BASE_URL ?>/home" class="btn-back" title="Voltar">
            <span class="icon-back">↩</span>
        </a>
    </div>

    <div class="settings-container">
        
        <section class="settings-card">
            <h2 class="settings-title">configurações<br>de som</h2>

            <div class="sound-controls">
                <div class="control-group">
                    <span class="sound-icon"><i class="bi bi-music-note-beamed"></i></span>
                    <input type="range" id="bg-music" name="bg_music" min="0" max="100" value="50" class="custom-slider">
                </div>

                <div class="control-group">
                    <span class="sound-icon"><i class="bi bi-volume-up"></i></span>
                    <input type="range" id="sound-fx" name="sound_fx" min="0" max="100" value="50" class="custom-slider">
                </div>
            </div>
        </section>

        <a href="<?= BASE_URL ?>/developers" class="btn-devs">
            desenvolvedores <span class="info-icon">i</span>
        </a>

    </div>
</main>

<?php include __DIR__ . '/../footer.php'; ?>