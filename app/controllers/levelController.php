<?php

class LevelController
{
    private Level $level;

    public function __construct(PDO $db)
    {
        $this->level = new Level($db);
    }

    public function index()
    {
        $this->requireAdmin();

        $levels = $this->level->findAll();

        require '../app/views/admin/levels/index.php';
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