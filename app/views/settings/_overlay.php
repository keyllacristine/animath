<?php /* Configurações como overlay (não sai da tela do jogo). Abre com window.openSettings(). */ ?>
<div id="settings-overlay" class="settings-overlay" hidden role="dialog" aria-modal="true" aria-labelledby="settings-overlay-title">
    <section class="settings-card">
        <button type="button" class="settings-close" id="settings-close" title="Fechar" aria-label="Fechar configurações">
            <i class="bi bi-x-lg"></i>
        </button>
        <h2 class="settings-title" id="settings-overlay-title">configurações<br>de som</h2>
        <?php include __DIR__ . '/_sound_controls.php'; ?>
    </section>
</div>

<script>
(() => {
    const overlay = document.getElementById('settings-overlay');
    soundManager.bindControls(overlay);

    window.openSettings = () => {
        soundManager.syncControls();
        overlay.hidden = false;
        document.body.classList.add('modal-open');   // a VN ignora Enter/Espaço enquanto aberto
        document.getElementById('settings-close').focus();
    };
    window.closeSettings = () => {
        overlay.hidden = true;
        document.body.classList.remove('modal-open');
    };

    document.getElementById('settings-close').addEventListener('click', window.closeSettings);
    overlay.addEventListener('click', (e) => { if (e.target === overlay) window.closeSettings(); });   // clicou fora do cartão
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !overlay.hidden) window.closeSettings(); });
})();
</script>
