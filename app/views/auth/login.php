    <?php include __DIR__ . '/../header.php'; ?>
    <?php if (isset($error) && !empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <div class="container login">
        <div class="form login">
        <form action="<?= BASE_URL ?>/login" method="POST">
            
            <fieldset>
                <legend>entrar na conta</legend>

                <h1>bem-vindo de volta!</h1>
                <p>caso precise, peça ajuda para um adulto</p>

                <div>
                    <label for="username">seu nome</label><br>
                    <input type="text" id="username" name="username" required autocomplete="off">
                </div>

                <br>

                <div>
                    <label for="password">sua senha</label><br>
                    <input type="password" id="password" name="password" required>
                </div>
            </fieldset>
            <button type="submit">Vamos aprender! &rarr;</button>
        </form>
            <p>não tem uma conta? <a href="<?= BASE_URL ?>/register">crie uma aqui</a></p>
        </div>
         <div>
            <img src="<?= BASE_URL ?>/assets/img/login.png" alt="Ilustração dos bichinhos do Animath" width="300">
        </div>
    <div>
