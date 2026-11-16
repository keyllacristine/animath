
<?php include __DIR__ . '/../header.php'; ?>
<?php
// Vêm do GameController::show():
// $level, $quiz, $armacao, $finale, $replay, $prologue, $outro, $avatar, $gender, $username

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
    <!-- Quem já zerou o jogo pode pular a explicação e ir pras contas -->
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

        // Avatar da conta: troca imagens ({avatar}) e pronomes ({o|a}) nas falas
        vars: {
            avatar: <?= (int) $avatar ?>,
            gender: <?= json_encode($gender) ?>,
            nome: <?= json_encode($username, JSON_UNESCAPED_UNICODE) ?>,
        },
    });

    window.vn = vn;

    // HUD do jogo: voltar e configurações
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
        // Saiu no meio do quiz: as respostas ainda não foram salvas
        if (quizStarted && !quizFinished && !confirm('Sair do nível? As respostas deste quiz não serão salvas.')) return;

        setTimeout(() => {
            window.location.href = BASE + '/levels';
        }, 150);
    });

    let quizFinished = false;
    let quizStarted = false;

    // Salva o progresso no servidor
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

    // Mensagem final
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

    // Transição para a tela de níveis
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

    // Armação opcional
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

    // Fluxo do nível: prólogo → cena → quiz → conclusão
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
            // Próximo passo: cena do nível
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
            // Primeira vez: cena de armação antes do quiz
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
                // Sem prólogo cadastrado: segue direto
            }
        }

        if (!PROLOGUE || !vn.scene) {
            // Reentrada: começa na parte 2, sem o contexto geral
            const scene = await vn.loadScene(SLUG);

            await vn.play(scene, {
                at: REPLAY ? vn.part2Index(scene) : 0
            });
        }
    } catch (err) {
        // Cena ainda não existe: começa vazia
        await vn.play({
            slug: SLUG,
            level_id: LEVEL_ID,
            title: SLUG,
            lines: []
        });
    }

    // Nível sem cena cadastrada: vai direto para o quiz
    if (!vn.scene.lines.length) {
        startQuiz();
    }

    document.getElementById('vn-skip')?.addEventListener('click', startQuiz);

})();
</script>

<?php include __DIR__ . '/../footer.php'; ?>
