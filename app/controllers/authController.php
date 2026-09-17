<?php

class AuthController
{
    private User $user;

    public function __construct(PDO $db)
    {
        $this->user = new User($db);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require '../app/views/auth/login.php';
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->user->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Usuário ou senha inválidos.';
            require '../app/views/auth/login.php';
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['role'] = $user['role'];

        header('Location: /home');
        exit;
    }

    public function logout()
    {
        session_destroy();

        header('Location: /login');
        exit;
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require '../app/views/auth/register.php';
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $avatar = (int) ($_POST['avatar'] ?? 1);

        $this->user->create($username, $password, $avatar);

        header('Location: /login');
        exit;
    }
}