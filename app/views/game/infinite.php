<?php include __DIR__ . '/../header.php'; ?>
<?php
// Sprites por pose: avatar_1_idle.png, avatar_1_catch.png, avatar_1_good.png, avatar_1_bad.png (idem avatar_2_*)
$spriteDir = BASE_URL . '/assets/img/vn/chars/';
$sprites = [];
foreach (['idle', 'catch', 'good', 'bad'] as $pose) {
    $sprites[$pose] = $spriteDir . 'avatar_' . (int) $avatar . '_' . $pose . '.png';
}
$sprite = $sprites['idle'];
// Ícones de vida (HP): se os arquivos existirem em public/assets/img/, o jogo usa as imagens;
// senão continua com os corações de texto (♥ / ♡).
$imgDir = __DIR__ . '/../../../public/assets/img/';
$lifeFull  = is_file($imgDir . 'vida.png')       ? BASE_URL . '/assets/img/vida.png'       : '';
$lifeEmpty = is_file($imgDir . 'vida_vazia.png') ? BASE_URL . '/assets/img/vida_vazia.png' : '';

$jsConfig = $infinite + [
    'gender'    => $gender,
    'username'  => $username,
    'lifeFull'  => $lifeFull,
    'lifeEmpty' => $lifeEmpty,
    'sprites'   => $sprites,
];
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/infinite.css">

<div class="ig-wrap">
    <div class="ig-stage" id="igStage">

        <div class="ig-field" id="igField">
            <div class="ig-rabbit" id="igRabbit">
                <div class="ig-flip"><img src="<?= $sprite ?>" alt=""></div>
            </div>
        </div>
        <div class="ig-flash" id="igFlash"></div>

        <a href="<?= $arcadeUrl ?>" class="ig-circle ig-back" title="Voltar">↩</a>
        <div class="ig-hud ig-lives" id="igLives"></div>
        <div class="ig-hud ig-score">pontos: <b id="igScore">0</b></div>
        <div class="ig-hud ig-question" id="igQuestion"></div>
        <div class="ig-hud ig-timer" id="igTimer">
            <div class="ig-time" id="igTime">1:00</div>
            <div class="ig-bar"><i id="igBar"></i></div>
        </div>
        <button type="button" class="ig-circle ig-pausebtn" id="igPauseBtn" title="Pausar"></button>

        <div class="ig-count" id="igCount"></div>

        <div class="ig-touch">
            <button type="button" id="igLeft">◀</button>
            <button type="button" id="igRight">▶</button>
        </div>

        <div class="ig-overlay" id="igStart">
            <div class="ig-card">
                <h2><?= htmlspecialchars($infinite['title']) ?></h2>
                <div class="ig-howto">
                    <p>mova com ← → (ou A e D) e pegue só o resultado certo da conta!</p>
                </div>
                <p>você tem <?= (int) $infinite['lives'] ?> vidas e <?= (int) $infinite['time'] ?> segundos. pegar um resultado errado tira uma vida.</p>
                <p>recorde: <b id="igStartBest">0</b></p>
                <div class="ig-actions">
                    <button type="button" class="ig-btn" id="igStartBtn">começar</button>
                    <a href="<?= $arcadeUrl ?>" class="ig-btn alt">voltar</a>
                </div>
            </div>
        </div>

        <div class="ig-overlay" id="igPause">
            <div class="ig-card">
                <h2>jogo pausado</h2>
                <div class="ig-actions">
                    <button type="button" class="ig-btn" id="igResumeBtn">continuar</button>
                    <button type="button" class="ig-btn alt" id="igRestartBtn">recomeçar</button>
                    <button type="button" class="ig-btn alt" id="igSoundBtn"><i class="bi bi-volume-up-fill"></i> som</button>
                    <a href="<?= $arcadeUrl ?>" class="ig-btn alt">sair</a>
                </div>
            </div>
        </div>

        <div class="ig-overlay" id="igEnd">
            <div class="ig-card">
                <h2 id="igEndTitle"></h2>
                <p id="igEndMsg"></p>
                <div class="ig-stats">
                    <div class="ig-stat score"><b id="igEndScore">0</b>pontos</div>
                    <div class="ig-stat best"><b id="igEndBest">0</b>recorde</div>
                    <div class="ig-stat"><b id="igEndHits">0</b>acertos</div>
                    <div class="ig-stat"><b id="igEndErrors">0</b>erros</div>
                </div>
                <div class="ig-actions">
                    <button type="button" class="ig-btn" id="igAgainBtn">jogar de novo</button>
                    <a href="<?= $arcadeUrl ?>" class="ig-btn alt">início</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>

<script>
    window.INFINITE_CONFIG = <?= json_encode($jsConfig, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="<?= BASE_URL ?>/assets/js/infinite.js"></script>

<?php include __DIR__ . '/../footer.php'; ?>