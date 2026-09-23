<?php include_once __DIR__ . '/../header.php'?>
<div class="container register">
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
            <label>
                <input type="radio" name="avatar" value="1" checked>
                <img src="<?= BASE_URL ?>/assets/img/av1.png" alt="Avatar Coelho Coelho com Ursinho" width="100">
            </label>
            <label>
                <input type="radio" name="avatar" value="2">
                <img src="<?= BASE_URL ?>/assets/img/av2.png" alt="Avatar Coelho com Laço" width="115">
            </label>
        </fieldset>
        <button type="submit" class="btn lets-learn">Vamos aprender! &rarr;</button>
    </div>
    </form>
</div>
    <?php include_once __DIR__ . '/../footer.php'?>