<?php include __DIR__ . '/../header.php'; ?>
<?php
// $level e $quiz vêm do GameController::show()
$levelId = (int) $level['id'];
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$slug    = 'level_' . $levelId . '_intro';   // cena de introdução do nível
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/vn.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/vn-quiz.css">
<style>
    body { background: #1b0a1c; }
</style>

<div id="vn-root"></div>

<?php if ($isAdmin): ?>
    <!-- só admin: pula a VN e testa o quiz direto -->
    <button type="button" class="vn-btn" id="vn-skip"
            style="position:fixed;left:16px;bottom:16px;z-index:10001">⏭ Ir pro quiz</button>
<?php endif; ?>

<script src="<?= BASE_URL ?>/assets/js/vn-engine.js"></script>
<script src="<?= BASE_URL ?>/assets/js/vn-quiz.js"></script>
<?php if ($isAdmin): ?>
    <script src="<?= BASE_URL ?>/assets/js/vn-editor.js"></script>
<?php endif; ?>

<script>
(async () => {
    const BASE     = <?= json_encode(BASE_URL) ?>;
    const SLUG     = <?= json_encode($slug) ?>;
    const LEVEL_ID = <?= $levelId ?>;
    const IS_ADMIN = <?= $isAdmin ? 'true' : 'false' ?>;
    const QUIZ     = <?= json_encode($quiz) ?>;

    const vn = new VNEngine({
        root:       document.getElementById('vn-root'),
        assetsBase: BASE + '/assets/img/vn',
        sceneUrl:   BASE + '/vn/scene',
        saveUrl:    BASE + '/vn/save',
    });
    window.vn = vn;   // vn.insertScene('...') continua disponível

    // Salva o progresso no servidor (GameController::complete)
    async function saveProgress({ hits, errors }) {
        const res = await fetch(BASE + '/game/complete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ level_id: LEVEL_ID, hits, errors }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.error || res.status);
    }

    // FLUXO DO NÍVEL: cena de VN → quiz → (salva) → tela de níveis
    let quizStarted = false;
    function startQuiz() {
        if (quizStarted) return;
        quizStarted = true;
        vn.stop();   // trava Enter/clique da VN enquanto o quiz roda
        new VNQuiz({
            stage:      vn.stage,
            assetsBase: BASE + '/assets/img/vn',
            config:     QUIZ,
            backUrl:    BASE + '/levels',
            onFinish:   saveProgress,
        }).start();
    }

    // só a cena principal dispara o quiz (uma dica inserida com insertScene não)
    vn.on('end', (scene) => { if (scene.slug === SLUG) startQuiz(); });

    try {
        await vn.play(SLUG);
    } catch (err) {
        // cena ainda não existe: começa vazia (admin cria pelo editor e salva)
        await vn.play({ slug: SLUG, level_id: LEVEL_ID, title: SLUG, lines: [] });
    }

    // nível sem cena ainda → vai direto pro quiz (admin precisa do editor aberto, então não pula)
    if (!vn.scene.lines.length && !IS_ADMIN) startQuiz();

    if (IS_ADMIN) {
        document.getElementById('vn-skip').addEventListener('click', startQuiz);
        new VNEditor(vn, {
            assetsUrl: BASE + '/vn/assets',
            uploadUrl: BASE + '/vn/upload',
        });
    }
})();
</script>
<?php include __DIR__ . '/../footer.php'; ?>
