<?php

class LevelController
{
    private Level $level;
    private Progress $progress;

    public function __construct(PDO $db)
    {
        $this->level = new Level($db);
        $this->progress = new Progress($db);
    }

    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $levels = $this->level->findAll();
        
        // Busca o progresso do usuário logado
        $userProgressData = $userId ? $this->progress->findByUser($userId) : [];
        
        // Mapeia o progresso indexado pelo level_id para fácil acesso
        $userProgress = [];
        foreach ($userProgressData as $p) {
            $userProgress[$p['level_id']] = $p;
        }

        require '../app/views/levels/index.php';
    }

    public function create()
    {
        $this->requireAdmin();

        $id = $this->level->create();

        header('Location: /admin/levels');
        exit;
    }

    public function delete()
    {
        $this->requireAdmin();

        $id = (int) ($_POST['id'] ?? 0);

        $this->level->delete($id);

        header('Location: /admin/levels');
        exit;
    }

    private function requireAdmin()
    {
        if (
            !isset($_SESSION['user_id']) ||
            ($_SESSION['role'] ?? '') !== 'admin'
        ) {
            http_response_code(403);
            exit('Acesso negado.');
        }
    }
}