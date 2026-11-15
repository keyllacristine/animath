    <?php include __DIR__ . '/../header.php'; ?>
    <?php if (isset($error) && !empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <div class="container login">
    <div class="login-header">
        <a href="<?= BASE_URL ?>/home" class="btn-circle btn-back" title="Voltar">
            <i class="bi bi-reply-fill"></i>
        </a>        
        <button type="button" class="btn-circle btn-settings" title="Configurações" aria-label="Abrir configurações" onclick="window.openSettings()">
            <i class="bi bi-gear-fill"></i>
        </button>
    </div>
        <form action="<?= BASE_URL ?>/login" method="POST" class="login">
        <div class="form login">
            <fieldset class="login">
                <legend>entrar na conta</legend>

                <h1>bem-vindo de volta!</h1>
                <p class="hint help">caso precise, peça ajuda para um adulto</p>

                
                    <label for="username">seu nome</label><br>
                    <input type="text" id="username" name="username" required autocomplete="off">
                <br>
                    <label for="password">sua senha</label><br>
                    <input type="password" id="password" name="password" required>
                    <p class="hint register">não tem uma conta? <a href="<?= BASE_URL ?>/register" class="hint register">crie uma aqui</a></p>
            </fieldset>
        </div>
            <div class="form login">
                <img src="<?= BASE_URL ?>/assets/img/animath_logo.png" class="login-art" alt="Ilustração dos bichinhos do Animath" width="300" >
                <button type="submit" class="btn lets-learn">Vamos aprender! &rarr;</button>
            </div>
        </form>        
    <div>
<?php include __DIR__ . '/../settings/_overlay.php'; ?>
