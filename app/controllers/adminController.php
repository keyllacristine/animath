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
}