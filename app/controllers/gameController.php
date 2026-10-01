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

        require '../app/views/game/levels.php';
    }

    public function complete()
    {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];

        $levelId = (int) ($_POST['level_id'] ?? 0);
        $hits = (int) ($_POST['hits'] ?? 0);
        $errors = (int) ($_POST['errors'] ?? 0);

        // cria ou atualiza o progresso do usuário
        $this->progress->save(
            $userId,
            $levelId,
            $hits,
            $errors
        );

        echo json_encode([
            'success' => true
        ]);
    }

    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}