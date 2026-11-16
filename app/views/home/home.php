<?php include __DIR__ . '/../header.php'; ?>
<?php $isLoggedIn = isset($_SESSION['user_id']);
$arcadeTitle = (require __DIR__ . '/../../../config/quiz.php')['arcade']['title'];

$infiniteUnlocked = $isLoggedIn
    && (new Progress($db))->hasCompletedAll((int) $_SESSION['user_id']);

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
    <header class="header-logado">
    <img class="logo" src="<?= BASE_URL ?>/assets/img/animath_logo.png" alt="Logo do site">
        <a href="<?= BASE_URL ?>/settings" class="btn-circle btn-settings" title="Configurações">
            <span class="icon-gear"><i class="bi bi-gear-fill"></i></span>
        </a>
    </header>
    <div class="sidebar-logado">
        <nav class="nav logado">
            <div>
                <a href="<?= BASE_URL ?>/levels">
                    <button type="button" onclick="soundManager.playClickSound()">Jogar <i class="bi bi-play-fill"></i></button>
                </a>
            </div>
             <div>
                <?php if ($infiniteUnlocked): ?>
                <a href="<?= BASE_URL ?>/game/arcade">
                    <button type="button" onclick="soundManager.playClickSound()"><?= htmlspecialchars($arcadeTitle) ?> <i class="bi bi-controller"></i></button>
                </a>
                <?php else: ?>
                <button type="button" class="is-locked" disabled title="conclua todos os níveis para liberar"><?= htmlspecialchars($arcadeTitle) ?> <i class="bi bi-lock-fill"></i></button>
                <?php endif; ?>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/profile">
                    <button type="button" onclick="soundManager.playClickSound()">Meu perfil <i class="bi bi-person-fill"></i></button>
                </a>
            </div>
        </nav>
    </div>
</div>

<?php else: ?>
<div class="container nao-logado">
    <img class="logo" src="<?= BASE_URL ?>/assets/img/animath_logo.png" alt="Logo do site">
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