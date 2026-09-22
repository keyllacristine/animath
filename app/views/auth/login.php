    <?php if (isset($error) && !empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

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

        <br>

        <div>
            <img src="<?= BASE_URL ?>/assets/images/bichinhos-login.png" alt="Ilustração dos bichinhos do Animath" width="300">
        </div>

        <br>

        <button type="submit">Vamos aprender! &rarr;</button>

    </form>
