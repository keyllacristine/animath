<?php include __DIR__ . '/../header.php'; ?>
<?php
$gameTitle = 'balança';
$howto = 'deixe a balança equilibrada! clique nos pesinhos para colocá-los do lado direito até ficar igual ao lado esquerdo. clique de novo para tirar.';
$sprite = BASE_URL . '/assets/img/vn/chars/av' . (int) $avatar . '.png';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/infinite.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/arcade.css">

<div class="ig-wrap">
    <div class="ig-stage" id="igStage">
        <div class="ig-field bg-porta dim" id="igField">
            <div class="bl-post"></div>
            <div class="bl-base"></div>
            <div class="bl-beam" id="blBeam">
                <div class="bl-hang bl-left"><div class="bl-chain"></div><div class="bl-pan" id="blLeft"></div></div>
                <div class="bl-hang bl-right"><div class="bl-chain"></div><div class="bl-pan" id="blRight"></div></div>
            </div>
            <div class="bl-pivot"></div>
            <div class="bl-tray" id="blTray"></div>
            <img class="ar-mascot" src="<?= $sprite ?>" alt="" id="arMascot">
        </div>
        <div class="ig-hud ig-question ar-hint" id="arHint">deixe os dois lados iguais!</div>
        <?php include __DIR__ . '/_shell.php'; ?>
    </div>
</div>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>

<script>
    window.ARCADE_CONFIG = <?= json_encode($arcadeCfg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="<?= BASE_URL ?>/assets/js/arcade-core.js"></script>
<script src="<?= BASE_URL ?>/assets/js/arcade-balance.js"></script>

<?php include __DIR__ . '/../footer.php'; ?>
