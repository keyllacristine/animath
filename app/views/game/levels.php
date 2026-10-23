<?php include __DIR__ . '/../header.php'; ?>
<?php
// Vêm do GameController::show():
//   $level, $quiz, $finale, $replay (já zerou o jogo?), $prologue, $outro, $avatar (1|2), $gender ('m'|'f'), $username
$levelId = (int) $level['id'];
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$slug    = 'level_' . $levelId . '_intro';   // cena do nível (parte 1 = contexto, parte 2 = explicação)
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/vn.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/vn-quiz.css">
<style>
    body { background: #1b0a1c; }
</style>

<div id="vn-root"></div>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>

<?php if ($isAdmin || $replay): ?>
    <!-- admin: testa o quiz direto / quem já zerou o jogo: pula a explicação e vai pras contas -->
    <button type="button" class="vn-btn" id="vn-skip"
            style="position:fixed;left:16px;bottom:16px;z-index:10001">
        <?= $replay ? 'pular para as contas ⏭' : '⏭ Ir pro quiz' ?>
    </button>
<?php endif; ?>

<script src="<?= BASE_URL ?>/assets/js/vn-engine.js"></script>
<script src="<?= BASE_URL ?>/assets/js/vn-quiz.js"></script>
<?php if ($isAdmin): ?>
    <script src="<?= BASE_URL ?>/assets/js/vn-editor.js"></script>
<?php endif; ?>

<script>
(async () => {
    const BASE       = <?= json_encode(BASE_URL) ?>;
    const SLUG       = <?= json_encode($slug) ?>;
    const FINAL_SLUG = 'game_final';   // cena opcional exibida antes da mensagem final
    const PROLOGUE   = <?= json_encode($prologue) ?>;   // 'intro' (1ª vez no nível 1) ou ''
    const OUTRO      = <?= json_encode($outro) ?>;      // cena de transição depois do quiz ('' na reentrada)
    const LEVEL_ID   = <?= $levelId ?>;
    const IS_ADMIN   = <?= $isAdmin ? 'true' : 'false' ?>;
    const REPLAY     = <?= $replay ? 'true' : 'false' ?>;
    const QUIZ       = <?= json_encode($quiz) ?>;
    const FINALE     = <?= json_encode($finale, JSON_UNESCAPED_UNICODE) ?>;

    const vn = new VNEngine({
        root:       document.getElementById('vn-root'),
        assetsBase: BASE + '/assets/img/vn',
        sceneUrl:   BASE + '/vn/scene',
        saveUrl:    BASE + '/vn/save',
        // avatar da conta: troca imagens ({avatar}) e pronomes ({o|a}) nas falas
        vars: {
            avatar: <?= (int) $avatar ?>,
            gender: <?= json_encode($gender) ?>,
            nome:   <?= json_encode($username, JSON_UNESCAPED_UNICODE) ?>,
        },
    });
    window.vn = vn;   // vn.insertScene('...') continua disponível

    // HUD do jogo: voltar (esq.) e configurações (dir.) — dentro da cena, escalam junto com ela
    const hud = document.createElement('div');
    hud.className = 'game-hud';
    hud.innerHTML = `
        <button type="button" class="hud-btn hud-back" title="Voltar aos níveis" aria-label="Voltar aos níveis"><i class="bi bi-reply-fill"></i></button>
        <button type="button" class="hud-btn hud-settings" title="Configurações" aria-label="Abrir configurações"><i class="bi bi-gear-fill"></i></button>`;
    vn.stage.append(hud);
    hud.addEventListener('click', (e) => e.stopPropagation());   // clicar nos botões não avança a fala

    hud.querySelector('.hud-settings').addEventListener('click', () => window.openSettings());
    hud.querySelector('.hud-back').addEventListener('click', () => {
        // saiu no meio do quiz: as respostas ainda não foram salvas
        if (quizStarted && !quizFinished && !confirm('Sair do nível? As respostas deste quiz não serão salvas.')) return;
        setTimeout(() => { window.location.href = BASE + '/levels'; }, 150);   // dá tempo do som de clique
    });

    let quizFinished = false;
    let quizStarted = false;

    // Salva o progresso no servidor (GameController::complete) e devolve { finale, next_level_id }
    async function saveProgress({ hits, errors }) {
        const res = await fetch(BASE + '/game/complete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ level_id: LEVEL_ID, hits, errors }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.error || res.status);
        quizFinished = true;   // progresso salvo: pode sair sem aviso
        return data;
    }

    // MENSAGEM FINAL: cena 'game_final' (se existir) → cartão com botão para a tela inicial
    let quiz = null;
    function showFinalCard() {
        VNQuiz.showEnd(vn.stage, {
            title:  vn.fmt(FINALE.title  || 'parabéns!'),
            text:   vn.fmt(FINALE.text   || 'você concluiu todos os níveis!'),
            button: vn.fmt(FINALE.button || 'ir para a tela inicial'),
            href:   BASE + '/home',
        });
    }
    async function playFinale() {
        quiz?.destroy();
        try {
            await vn.play(FINAL_SLUG);                 // o 'end' dessa cena mostra o cartão
            if (!vn.scene.lines.length) showFinalCard();
        } catch {
            showFinalCard();                           // sem cena final: vai direto pro cartão
        }
    }

    // TRANSIÇÃO: cena depois do quiz → tela de níveis (o próximo nível já está liberado)
    const goLevels = () => { window.location.href = BASE + '/levels'; };
    async function playOutro() {
        quiz?.destroy();
        try {
            await vn.play(OUTRO);                      // o 'end' dessa cena redireciona
            if (!vn.scene.lines.length) goLevels();
        } catch {
            goLevels();                                // sem cena de transição: volta direto
        }
    }

    // FLUXO DO NÍVEL: [prólogo] → cena de VN → quiz → (salva) → tela de níveis / próximo nível / mensagem final
    function startQuiz() {
        if (quizStarted) return;
        quizStarted = true;
        document.getElementById('vn-skip')?.remove();
        vn.stop();   // trava Enter/clique da VN enquanto o quiz roda
        quiz = new VNQuiz({
            stage:      vn.stage,
            assetsBase: BASE + '/assets/img/vn',
            config:     { ...QUIZ, mascot: vn.fmt(QUIZ.mascot || '') },
            backUrl:    BASE + '/levels',
            levelUrl:   (id) => BASE + '/game?id=' + id,
            onFinish:   saveProgress,
            onFinale:   playFinale,
            onOutro:    OUTRO ? playOutro : null,
        });
        quiz.start();
    }

    vn.on('end', (scene) => {
        if (scene.slug === PROLOGUE) {                   // prólogo acabou → cena do nível
            vn.play(SLUG).catch(async () => {
                await vn.play({ slug: SLUG, level_id: LEVEL_ID, title: SLUG, lines: [] });
                if (!IS_ADMIN) startQuiz();
            });
        }
        else if (scene.slug === SLUG) startQuiz();       // só a cena principal dispara o quiz
        else if (scene.slug === FINAL_SLUG) showFinalCard();
        else if (scene.slug === OUTRO) goLevels();
    });

    try {
        if (PROLOGUE) {
            try { await vn.play(PROLOGUE); } catch { /* sem prólogo cadastrado: segue direto */ }
        }
        if (!PROLOGUE || !vn.scene) {
            // Reentrada (já zerou o jogo): começa na parte 2, sem o contexto geral
            const scene = await vn.loadScene(SLUG);
            await vn.play(scene, { at: REPLAY ? vn.part2Index(scene) : 0 });
        }
    } catch (err) {
        // cena ainda não existe: começa vazia (admin cria pelo editor e salva)
        await vn.play({ slug: SLUG, level_id: LEVEL_ID, title: SLUG, lines: [] });
    }

    // nível sem cena ainda → vai direto pro quiz (admin precisa do editor aberto, então não pula)
    if (!vn.scene.lines.length && !IS_ADMIN) startQuiz();

    document.getElementById('vn-skip')?.addEventListener('click', startQuiz);

    if (IS_ADMIN) {
        new VNEditor(vn, {
            assetsUrl: BASE + '/vn/assets',
            uploadUrl: BASE + '/vn/upload',
        });
    }
})();
</script>
<?php include __DIR__ . '/../footer.php'; ?>
