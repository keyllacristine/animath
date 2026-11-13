<a href="<?= $arcadeUrl ?>" class="ig-circle ig-back" title="Voltar">↩</a>
<div class="ig-hud ig-score solo">pontos: <b id="igScore">0</b></div>
<div class="ig-hud ig-timer" id="igTimer">
    <div class="ig-time" id="igTime">1:00</div>
    <div class="ig-bar"><i id="igBar"></i></div>
</div>
<button type="button" class="ig-circle ig-pausebtn" id="igPauseBtn" title="Pausar"></button>

<div class="ig-count" id="igCount"></div>

<div class="ig-overlay" id="igStart">
    <div class="ig-card">
        <h2><?= htmlspecialchars($gameTitle) ?></h2>
        <div class="ig-howto">
            <p><?= htmlspecialchars($howto) ?></p>
        </div>
        <p>você tem <?= (int) $arcadeCfg['time'] ?> segundos. quanto mais rápido, mais pontos!</p>
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
        <div class="ig-stats" id="igEndStats"></div>
        <div class="ig-actions">
            <button type="button" class="ig-btn" id="igAgainBtn">jogar de novo</button>
            <a href="<?= $arcadeUrl ?>" class="ig-btn alt">início</a>
        </div>
    </div>
</div>
