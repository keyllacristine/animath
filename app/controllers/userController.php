<?php

class UserController
{
    private User $user;

    public function __construct(PDO $db)
    {
        $this->user = new User($db);
    }

    public function index()
    {
        $this->requireAdmin();

        $users = $this->user->findAll();

        require '../app/views/admin/users/index.php';
    }

    public function create()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require '../app/views/admin/users/create.php';
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $avatar = (int) ($_POST['avatar'] ?? 1);
        $role = $_POST['role'] ?? 'user';

        $this->user->create(
            $username,
            $password,
            $avatar,
            $role
        );

        header('Location: /admin/users');
        exit;
    }

    public function edit()
    {
        $this->requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $user = $this->user->findById($id);

            require '../app/views/admin/users/edit.php';
            return;
        }

        $username = $_POST['username'] ?? '';
        $avatar = (int) ($_POST['avatar'] ?? 1);
        $password = $_POST['password'] ?? null;
        $role = $_POST['role'] ?? 'user';

        $this->user->update(
            $id,
            $username,
            $avatar,
            $password,
            $role
        );

        header('Location: /admin/users');
        exit;
    }

    public function delete()
    {
        $this->requireAdmin();

        $id = (int) ($_POST['id'] ?? 0);

        $this->user->delete($id);

        header('Location: /admin/users');
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