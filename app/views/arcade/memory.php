<?php include __DIR__ . '/../header.php'; ?>
<?php
$gameTitle = 'memória';
$howto = 'memorize as cartas: cada conta tem um resultado da mesma cor. quando elas virarem, encontre os pares!';
$sprite = BASE_URL . '/assets/img/vn/chars/av' . (int) $avatar . '.png';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/infinite.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/arcade.css">

<div class="ig-wrap">
    <div class="ig-stage" id="igStage">
        <div class="ig-field bg-pov dim" id="igField">
            <div class="mm-grid" id="mmGrid"></div>
            <img class="ar-mascot" src="<?= $sprite ?>" alt="" id="arMascot">
        </div>
        <div class="ig-hud ig-question ar-hint" id="arHint"></div>
        <?php include __DIR__ . '/_shell.php'; ?>
    </div>
</div>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>

<script>
    window.ARCADE_CONFIG = <?= json_encode($arcadeCfg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="<?= BASE_URL ?>/assets/js/arcade-core.js"></script>
<script src="<?= BASE_URL ?>/assets/js/arcade-memory.js"></script>

<?php include __DIR__ . '/../footer.php'; ?>
