<?php include_once __DIR__ . '/../header.php'?>
<div class="container register">
     <div class="login-header">
        <a href="<?= BASE_URL ?>/home" class="btn-circle btn-back" title="Voltar">
            <i class="bi bi-reply-fill"></i>
        </a>        
        <button type="button" class="btn-circle btn-settings" title="Configurações" aria-label="Abrir configurações" onclick="window.openSettings()">
            <i class="bi bi-gear-fill"></i>
        </button>
    </div>
<form action="<?= BASE_URL ?>/register" method="POST" class="register">
    <div class="form register">
        <fieldset class="register">
            <legend>dados da conta</legend>
            <h1>vamos criar sua conta!</h1>
            <p class="hint help">caso precise, peça ajuda para um adulto</p>
                <label for="username">seu nome</label><br>
                <input type="text" id="username" name="username" required autocomplete="off">
                <br>
                <label for="password">sua senha</label><br>
                <input type="password" id="password" name="password" required>
        </fieldset>
    </div>
    <div class="form register">
        <fieldset class="choose-avatar">
            <legend>escolha um avatar</legend>
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
        </fieldset>
        <button type="submit" class="btn lets-learn">Vamos aprender! &rarr;</button>
    </div>
    </form>
</div>
<?php include __DIR__ . '/../settings/_overlay.php'; ?>
    <?php include_once __DIR__ . '/../footer.php'?>