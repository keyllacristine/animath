<?php include __DIR__ . '/../header.php'; ?>
<?php $isLoggedIn = isset($_SESSION['user_id']);

$bgImage = $isLoggedIn 
    ? BASE_URL . '/assets/img/logado.png' 
    : BASE_URL . '/assets/img/nao-logado.png';
?>
<style>
    body {
        background-image: url('<?= $bgImage ?>');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center right;
        background-attachment: fixed;
    }
</style>

<?php if ($isLoggedIn): ?>
<div class="container logado">
    <div class="sidebar-logado">
        <h1>AniMath</h1>
        <nav class="nav logado">
            <div>
                <a href="<?= BASE_URL ?>/game">
                    <button type="button">Jogar <i class="bi bi-play-fill"></i></button>
                </a>
            </div>
             <div>
                <a href="<?= BASE_URL ?>/game/infinite">
                    <button type="button">Modo infinito <i class="bi bi-infinity"></i></button>
                </a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/profile">
                    <button type="button">Meu perfil <i class="bi bi-person-fill"></i></button>
                </a>
            </div>
           
            <div>
                <a href="<?= BASE_URL ?>/settings">
                    <button type="button">Configurações <i class="bi bi-gear-fill"></i></button>
                </a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/logout">
                    <button type="button">Sair <i class="bi bi-box-arrow-right"></i></button>
                </a>
            </div>
        </nav>
    </div>
</div>

<?php else: ?>
<div class="container nao-logado">
    <h1>AniMath</h1>
    <nav class="nav nao-logado">
        <div>
            <a href="<?= BASE_URL ?>/register">
                <button type="button" class="btn lets-learn">Vamos aprender!</button>
            </a>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/login">
                <button type="button">já tenho um perfil ➔</button>
            </a>
        </div>
    </nav>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../footer.php'; ?>