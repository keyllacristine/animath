<?php include_once __DIR__ . '/../header.php'; ?>

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
        <form action="<?= BASE_URL ?>/register" method="POST" class="register">
            <fieldset class="register">
                <legend>dados da conta</legend>

                <div class="auth-fieldset-body">
                    <div class="auth-fields">
                        <h1>vamos criar sua conta!</h1>
                        <p class="hint help">caso precise, peça ajuda para um adulto</p>

                        <label for="username">seu nome</label><br>
                        <input type="text" id="username" name="username" required autocomplete="off">
                        <br>
                        <label for="password">sua senha</label><br>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="auth-side">
                        <p class="avatar-heading">escolha um avatar</p>
                        <div class="avatar-selection">
                            <label class="avatar-option">
                                <input type="radio" name="avatar" value="1" <?= (($userData['avatar'] ?? 1) == 1) ? 'checked' : '' ?>>
                                <div class="avatar-card">
                                    <img src="<?= BASE_URL ?>/assets/img/av1.png" alt="Avatar Coelho">
                                    <div class="overlay">
                                        <span class="checkmark"><i class="bi bi-check-lg"></i></span>
                                    </div>
                                </div>
                            </label>

                            <label class="avatar-option">
                                <input type="radio" name="avatar" value="2" <?= (($userData['avatar'] ?? 1) == 2) ? 'checked' : '' ?>>
                                <div class="avatar-card">
                                    <img src="<?= BASE_URL ?>/assets/img/av2.png" alt="Avatar Coelha">
                                    <div class="overlay">
                                        <span class="checkmark"><i class="bi bi-check-lg"></i></span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn lets-learn">Criar conta &rarr;</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>
<?php include_once __DIR__ . '/../footer.php'; ?>