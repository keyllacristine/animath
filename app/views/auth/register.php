<form action="<?= BASE_URL ?>/register" method="POST">
        <fieldset>
            <legend>dados da conta</legend>

            <h1>vamos criar sua conta!</h1>
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
        <fieldset>
            <legend>escolha um avatar</legend>

            <label>
                <input type="radio" name="avatar" value="1" checked>
                <img src="<?= BASE_URL ?>/assets/images/avatar1.png" alt="Avatar Coelho Coelho com Ursinho" width="100">
            </label>

            <label>
                <input type="radio" name="avatar" value="2">
                <img src="<?= BASE_URL ?>/assets/images/avatar2.png" alt="Avatar Coelho com Laço" width="100">
            </label>
        </fieldset>
        <br>
        <button type="submit">Vamos aprender! &rarr;</button>
    </form>