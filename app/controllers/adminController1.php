<?php

class AdminController
{
    public function index()
    {
        $this->requireAdmin();

        require '../app/views/admin/index.php';
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

    public function testCreateUser()
{
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: /login');
        exit;
    }

    $userModel = new User($this->db);

    $userModel->create(
        $_POST['username'],
        $_POST['password'],
        (int) $_POST['avatar'],
        $_POST['role']
    );

    header('Location: /admin/test?success=1');
    exit;
}
}