
<?php include __DIR__ . '/../header.php'; ?>
<?php

$levelId = (int) $level['id'];
$slug = 'level_' . $levelId . '_intro';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/vn.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/vn-quiz.css">

<style>
    body { background: #1b0a1c; }
</style>

<div id="vn-root"></div>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>

<?php if ($replay): ?>
    <button type="button" class="vn-btn" id="vn-skip"
            style="position:fixed;left:16px;bottom:16px;z-index:10001">
        <?= '⏭ pular para as contas' ?>
    </button>
<?php endif; ?>

<script src="<?= BASE_URL ?>/assets/js/vn-popup.js"></script>
<script src="<?= BASE_URL ?>/assets/js/vn-engine.js"></script>
<script src="<?= BASE_URL ?>/assets/js/vn-quiz.js"></script>

<script>
(async () => {
    const BASE       = <?= json_encode(BASE_URL) ?>;
    const SLUG       = <?= json_encode($slug) ?>;
    const FINAL_SLUG = 'game_final';
    const PROLOGUE   = <?= json_encode($prologue) ?>;
    const ARMACAO    = <?= json_encode($armacao) ?>;
    const OUTRO      = <?= json_encode($outro) ?>;
    const LEVEL_ID   = <?= $levelId ?>;
    const REPLAY     = <?= $replay ? 'true' : 'false' ?>;
    const QUIZ       = <?= json_encode($quiz) ?>;
    const FINALE     = <?= json_encode($finale, JSON_UNESCAPED_UNICODE) ?>;

    const vn = new VNEngine({
        root: document.getElementById('vn-root'),
        assetsBase: BASE + '/assets/img/vn',
        sceneUrl: BASE + '/vn/scene',

        vars: {
            avatar: <?= (int) $avatar ?>,
            gender: <?= json_encode($gender) ?>,
            nome: <?= json_encode($username, JSON_UNESCAPED_UNICODE) ?>,
        },
    });

    window.vn = vn;

    const hud = document.createElement('div');
    hud.className = 'game-hud';
    hud.innerHTML = `
        <button type="button" class="hud-btn hud-back" title="Voltar aos níveis" aria-label="Voltar aos níveis">
            <i class="bi bi-reply-fill"></i>
        </button>
        <button type="button" class="hud-btn hud-settings" title="Configurações" aria-label="Abrir configurações">
            <i class="bi bi-gear-fill"></i>
        </button>`;

    vn.stage.append(hud);

    hud.addEventListener('click', (e) => e.stopPropagation());

    hud.querySelector('.hud-settings').addEventListener('click', () => window.openSettings());

    hud.querySelector('.hud-back').addEventListener('click', () => {
        if (quizStarted && !quizFinished && !confirm('Sair do nível? As respostas deste quiz não serão salvas.')) return;

        setTimeout(() => {
            window.location.href = BASE + '/levels';
        }, 150);
    });

    let quizFinished = false;
    let quizStarted = false;

    async function saveProgress({ hits, errors }) {
        const res = await fetch(BASE + '/game/complete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ level_id: LEVEL_ID, hits, errors }),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || !data.success) {
            throw new Error(data.error || res.status);
        }

        quizFinished = true;
        return data;
    }

    let quiz = null;

    function showFinalCard() {
        VNQuiz.showEnd(vn.stage, {
            title: vn.fmt(FINALE.title || 'parabéns!'),
            text: vn.fmt(FINALE.text || 'você concluiu todos os níveis!'),
            button: vn.fmt(FINALE.button || 'ir para a tela inicial'),
            href: BASE + '/home',
        });
    }

    async function playFinale() {
        quiz?.destroy();

        try {
            await vn.play(FINAL_SLUG);

            if (!vn.scene.lines.length) {
                showFinalCard();
            }
        } catch {
            showFinalCard();
        }
    }

    const goLevels = () => {
        window.location.href = BASE + '/levels';
    };

    async function playOutro() {
        quiz?.destroy();

        try {
            await vn.play(OUTRO);

            if (!vn.scene.lines.length) {
                goLevels();
            }
        } catch {
            goLevels();
        }
    }

    let helpMode = false;

    function closeHelp() {
        if (!helpMode) return;

        helpMode = false;
        vn.stop();
        quiz.resume();
    }

    async function openArmacao() {
        if (!ARMACAO || !quiz || helpMode) return;

        helpMode = true;
        quiz.pause();

        try {
            await vn.play(ARMACAO);

            if (!vn.scene.lines.length) {
                closeHelp();
            }
        } catch {
            closeHelp();
        }
    }

    function startQuiz() {
        if (quizStarted) return;

        quizStarted = true;

        document.getElementById('vn-skip')?.remove();

        vn.stop();

        quiz = new VNQuiz({
            stage: vn.stage,
            assetsBase: BASE + '/assets/img/vn',
            config: { ...QUIZ, mascot: vn.fmt(QUIZ.mascot || '') },
            backUrl: BASE + '/levels',
            levelUrl: (id) => BASE + '/game?id=' + id,
            onFinish: saveProgress,
            onFinale: playFinale,
            onOutro: OUTRO ? playOutro : null,
            onHelp: ARMACAO ? openArmacao : null,
        });

        quiz.start();
    }

    vn.on('end', (scene) => {
        if (scene.slug === PROLOGUE) {
            vn.play(SLUG).catch(async () => {
                await vn.play({
                    slug: SLUG,
                    level_id: LEVEL_ID,
                    title: SLUG,
                    lines: []
                });

                startQuiz();
            });
        }
        else if (scene.slug === SLUG) {
            if (ARMACAO && !REPLAY) {
                vn.play(ARMACAO).catch(startQuiz);
            } else {
                startQuiz();
            }
        }
        else if (scene.slug === ARMACAO) {
            if (helpMode) {
                closeHelp();
            } else {
                startQuiz();
            }
        }
        else if (scene.slug === FINAL_SLUG) {
            showFinalCard();
        }
        else if (scene.slug === OUTRO) {
            goLevels();
        }
    });

    try {
        if (PROLOGUE) {
            try {
                await vn.play(PROLOGUE);
            } catch {
            }
        }

        if (!PROLOGUE || !vn.scene) {
            const scene = await vn.loadScene(SLUG);

            await vn.play(scene, {
                at: REPLAY ? vn.part2Index(scene) : 0
            });
        }
    } catch (err) {
        await vn.play({
            slug: SLUG,
            level_id: LEVEL_ID,
            title: SLUG,
            lines: []
        });
    }

    if (!vn.scene.lines.length) {
        startQuiz();
    }

    document.getElementById('vn-skip')?.addEventListener('click', startQuiz);

})();
</script>

<?php include __DIR__ . '/../footer.php'; ?>
