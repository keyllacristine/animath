<?php include __DIR__ . '/../header.php'; ?>
<?php
$gameTitle = 'sudoku';
$howto = 'toque numa casinha vazia e escolha o número (ou use as teclas 1 a 4). em cada linha, coluna e quadradinho não pode repetir 1, 2, 3 e 4!';
$sprite = BASE_URL . '/assets/img/vn/chars/av' . (int) $avatar . '.png';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/infinite.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/arcade.css">

<div class="ig-wrap">
    <div class="ig-stage" id="igStage">
        <div class="ig-field bg-pov dim" id="igField">
            <div class="sd-board" id="sdBoard"></div>
            <div class="sd-pad" id="sdPad"></div>
            <img class="ar-mascot" src="<?= $sprite ?>" alt="" id="arMascot">
        </div>
        <?php include __DIR__ . '/_shell.php'; ?>
    </div>
</div>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>

<script>
    window.ARCADE_CONFIG = <?= json_encode($arcadeCfg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="<?= BASE_URL ?>/assets/js/arcade-core.js"></script>
<script src="<?= BASE_URL ?>/assets/js/arcade-sudoku.js"></script>

<?php include __DIR__ . '/../footer.php'; ?>
