<?php

class ProgressController
{
    private Progress $progress;

    public function __construct(PDO $db)
    {
        $this->progress = new Progress($db);
    }

    public function index()
    {
        $this->requireAdmin();

        $progress = $this->progress->findAll();

        require '../app/views/admin/progress/index.php';
    }

    public function edit()
    {
        $this->requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $item = $this->progress->findById($id);

            require '../app/views/admin/progress/edit.php';
            return;
        }

        $completed = (int) ($_POST['completed'] ?? 0);
        $hits = (int) ($_POST['hits'] ?? 0);
        $errors = (int) ($_POST['errors'] ?? 0);

        $this->progress->update(
            $id,
            $completed,
            $hits,
            $errors
        );

        header('Location: /admin/progress');
        exit;
    }

    public function delete()
    {
        $this->requireAdmin();

        $id = (int) ($_POST['id'] ?? 0);

        $this->progress->delete($id);

        header('Location: /admin/progress');
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