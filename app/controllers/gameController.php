<?php

class GameController
{
    private Level $level;
    private Progress $progress;

    public function __construct(PDO $db)
    {
        $this->level = new Level($db);
        $this->progress = new Progress($db);
    }

    public function show()
    {
        $this->requireLogin();

        $levelId = (int) ($_GET['id'] ?? 0);

        $level = $this->level->findById($levelId);

        if (!$level) {
            http_response_code(404);
            exit('Nível não encontrado.');
        }

        // quem tenta abrir um nível bloqueado pela URL volta pra tela de níveis
        if (!$this->isUnlocked($levelId)) {
            header('Location: ' . BASE_URL . '/levels');
            exit;
        }

        // configuração do quiz deste nível (config/quiz.php)
        $quizConfig = require __DIR__ . '/../../config/quiz.php';
        $quiz = $quizConfig[$levelId] ?? $quizConfig['default'];
        $finale = $quizConfig['finale'] ?? [];

        // Cena de ARMAÇÃO da operação do nível (database/vn_armacao.sql): toca depois da explicação
        // e também pode ser reaberta pelo botão "armar a conta" durante o quiz.
        $armacaoNames = ['+' => 'adicao', '-' => 'subtracao', '*' => 'multiplicacao', '/' => 'divisao'];
        $armacao = isset($armacaoNames[$quiz['op'] ?? '']) ? 'armacao_' . $armacaoNames[$quiz['op']] : '';

        // Já concluiu o jogo todo? Então as cenas começam na parte 2 (explicação)
        // e aparece o botão de pular direto para as contas.
        $replay = $this->progress->hasCompletedAll((int) $_SESSION['user_id']);

        // Prólogo ('intro'): só na 1ª vez no nível 1 (enquanto o nível 1 não foi concluído).
        // Epílogo ('level_N_outro'): cena depois do quiz, só na jornada normal (não na reentrada).
        $firstLevelId = (int) ($this->level->findAll()[0]['id'] ?? 0);
        $thisDone = false;
        foreach ($this->progress->findByUser((int) $_SESSION['user_id']) as $p) {
            if ((int) $p['level_id'] === $levelId && !empty($p['completed'])) {
                $thisDone = true;
            }
        }
        $prologue = ($levelId === $firstLevelId && !$thisDone && !$replay) ? 'intro' : '';
        $outro    = $replay ? '' : 'level_' . $levelId . '_outro';

        // Avatar da conta (1 ou 2) e gênero correspondente (config/avatars.php)
        $avatars = require __DIR__ . '/../../config/avatars.php';
        $avatar = (int) ($_SESSION['avatar'] ?? 1);
        if (!isset($avatars[$avatar])) {
            $avatar = array_key_first($avatars);
        }
        $gender = $avatars[$avatar]['gender'];
        $username = (string) ($_SESSION['username'] ?? '');

        require '../app/views/game/levels.php';
    }

    // Modo infinito: só abre depois de concluir todos os níveis (admin pode abrir pra testar).
    // Por enquanto não tem conteúdo — a view é só um aviso.
    public function infinite()
    {
        $this->requireLogin();

        $isAdmin = ($_SESSION['role'] ?? '') === 'admin';

        if (!$isAdmin && !$this->progress->hasCompletedAll((int) $_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/levels');
            exit;
        }

        require '../app/views/game/infinite.php';
    }

    // Chamado pelo JS (vn-quiz.js) quando o quiz do nível termina
    public function complete()
    {
        $this->requireLogin();

        header('Content-Type: application/json; charset=utf-8');

        $userId = (int) $_SESSION['user_id'];

        $levelId = (int) ($_POST['level_id'] ?? 0);
        $hits = max(0, min(1000, (int) ($_POST['hits'] ?? 0)));
        $errors = max(0, min(1000, (int) ($_POST['errors'] ?? 0)));

        if (!$this->level->findById($levelId) || !$this->isUnlocked($levelId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'nível inválido']);
            return;
        }

        // antes de salvar: o jogo já estava todo concluído?
        $wasAllDone = $this->progress->hasCompletedAll($userId);

        // cria ou atualiza o progresso do usuário (nível concluído → libera o próximo)
        $this->progress->save($userId, $levelId, $hits, $errors);

        $allDone = $this->progress->hasCompletedAll($userId);

        // próximo nível (para o botão "próximo nível" no fim do quiz)
        $nextLevelId = null;
        $found = false;
        foreach ($this->level->findAll() as $l) {
            if ($found) {
                $nextLevelId = (int) $l['id'];
                break;
            }
            $found = ((int) $l['id'] === $levelId);
        }

        echo json_encode([
            'success'       => true,
            'finale'        => $allDone && !$wasAllDone,   // acabou de zerar o jogo → mensagem final
            'next_level_id' => $nextLevelId,
        ]);
    }

    // Mesma regra da tela de níveis: só abre se o nível anterior foi concluído.
    // Admin pode abrir qualquer nível (pra testar).
    private function isUnlocked(int $levelId): bool
    {
        if (($_SESSION['role'] ?? '') === 'admin') {
            return true;
        }

        $done = [];
        foreach ($this->progress->findByUser((int) $_SESSION['user_id']) as $p) {
            if (!empty($p['completed'])) {
                $done[(int) $p['level_id']] = true;
            }
        }

        $previous = null;
        foreach ($this->level->findAll() as $l) {
            $id = (int) $l['id'];

            if ($id === $levelId) {
                return $previous === null || isset($done[$previous]);
            }

            $previous = $id;
        }

        return false;
    }

    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}
