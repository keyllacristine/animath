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

        require '../app/views/game/levels.php';
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

        // cria ou atualiza o progresso do usuário (nível concluído → libera o próximo)
        $this->progress->save($userId, $levelId, $hits, $errors);

        echo json_encode(['success' => true]);
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
