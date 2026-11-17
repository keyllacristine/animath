<?php include __DIR__ . '/../header.php'; ?>

<main class="page-shell">
    <header class="page-header">
        <a href="<?= BASE_URL ?>/home" class="btn-circle btn-back" title="Voltar">
            <i class="bi bi-reply-fill"></i>
        </a>
        <button type="button" class="btn-circle btn-settings" title="Configurações" aria-label="Abrir configurações" onclick="window.openSettings()">
            <i class="bi bi-gear-fill"></i>
        </button>
    </header>

    <div class="page-content">
    <div class="profile-container">
        
        <section class="card-section">
            <header class="section-title">
                <h2>seu perfil <span class="icon-title"><i class="bi bi-person-fill"></i></span></h2>
            </header>

            <form action="<?= BASE_URL ?>/profile" method="POST" class="profile-form">
                <div class="username-field">
                    <label for="username">nome:</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($userData['username'] ?? $_SESSION['username'] ?? '') ?>" required>
                    <button type="submit" class="btn-edit" title="Salvar nome">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                </div>

                <div class="avatar-selection">
                    <label class="avatar-option">
                        <input type="radio" name="avatar" value="1" <?= (($userData['avatar'] ?? 1) == 1) ? 'checked' : '' ?>>
                        <div class="avatar-card">
                            <img src="<?= BASE_URL ?>/assets/img/av1.png" alt="Avatar Coelho com Ursinho">
                            <div class="overlay">
                                <span class="checkmark"><i class="bi bi-check-lg"></i></span>
                            </div>
                        </div>
                    </label>

                    <label class="avatar-option">
                        <input type="radio" name="avatar" value="2" <?= (($userData['avatar'] ?? 1) == 2) ? 'checked' : '' ?>>
                        <div class="avatar-card">
                            <img src="<?= BASE_URL ?>/assets/img/av2.png" alt="Avatar Coelho com Laço">
                            <div class="overlay">
                                <span class="checkmark"><i class="bi bi-check-lg"></i></span>
                            </div>
                        </div>
                    </label>
                </div>

                <button type="submit" id="save-avatar-btn" style="display: none;"></button>
            </form>
        </section>

        <section class="card-section">
            <header class="section-title">
                <h2>seu desempenho <span class="icon-title"><i class="bi bi-graph-up"></i></span></h2>
            </header>

            <div class="stats-container">
                <div class="stat-box hits">
                    <h3>acertos<i class="bi bi-check-lg"></i></h3>
                    <p class="stat-number"><?= $totalHits ?? 0 ?></p>
                </div>

                <div class="stat-box errors">
                    <h3>erros <i class="bi bi-x-lg"></i></h3>
                    <p class="stat-number"><?= $totalErrors ?? 0 ?></p>
                </div>
            </div>
        </section>

    </div>

        <div class="bottom-bar">
            <a href="<?= BASE_URL ?>/logout" class="btn-logout">
                sair da conta <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll('.avatar-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.getElementById('save-avatar-btn').click();
        });
    });
</script>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>
<?php include __DIR__ . '/../footer.php'; ?>