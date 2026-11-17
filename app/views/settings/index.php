<?php include __DIR__ . '/../header.php'; ?>

<main class="page-shell">
    <header class="page-header">
        <a href="<?= BASE_URL ?>/home" class="btn-circle btn-back btn-corner" title="Voltar">
            <i class="bi bi-reply-fill"></i>
        </a>
    </header>

    <div class="page-content">
        <div class="settings-container">

            <section class="settings-card">
                <h2 class="settings-title">configurações<br>de som</h2>

                <?php include __DIR__ . '/_sound_controls.php'; ?>
            </section>

            <a href="<?= BASE_URL ?>/developers" class="btn-devs">
                desenvolvedores <span class="info-icon">i</span>
            </a>

        </div>
    </div>
</main>
<script>soundManager.bindControls(document);</script>
<?php include __DIR__ . '/../footer.php'; ?>