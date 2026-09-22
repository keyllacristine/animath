<?php include __DIR__ . '/../header.php'; ?>
<?php $isLoggedIn = isset($_SESSION['user_id']);

$bgImage = $isLoggedIn 
    ? BASE_URL . '/assets/img/logado.png'  // Imagem para utilizador logado
    : BASE_URL . '/assets/img/nao-logado.png';   // Imagem para utilizador não logado
?>
<style>
        body {
            /* Aplica o background dinâmico */
            background-image: url('<?= $bgImage ?>');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            }
</style>
    <h1>AniMath</h1>

    <?php if ($isLoggedIn): ?>
        <nav>
            <div>
                <a href="<?= BASE_URL ?>/settings">
                <button type="button">Configuraçoes</button>
                </a>
            </div>

            <br>

            <div>
                <a href="<?= BASE_URL ?>/game">
                    <button type="button">Jogar</button>
                </a>
            </div>

            <br>

            <div>
                <a href="<?= BASE_URL ?>/profile">
                    <button type="button">Meu perfil</button>
                </a>
            </div>

            <br>

            <div>
                <a href="<?= BASE_URL ?>/game/infinite">
                    <button type="button">Modo infinito</button>
                </a>
            </div>

            <br>

            <div>
                <a href="<?= BASE_URL ?>/logout">
                    <button type="button">Sair</button>
                </a>
            </div>
        </nav>

    <?php else: ?>
        <nav>
            <div>
                <a href="<?= BASE_URL ?>/register">
                    <button type="button">Vamos aprender!</button>
                </a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/login">
                    <button type="button">já tenho um perfil ➔</button>
                </a>
            </div>
        </nav>

    <?php endif; ?>
<?php include __DIR__ . '/../footer.php'; ?>