<?php

class AdminController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function index()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }

        require __DIR__ . '/../views/admin/index.php';
    }

    public function test()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }

        $userModel = new User($this->db);
        $levelModel = new Level($this->db);
        $progressModel = new Progress($this->db);

        $users = $userModel->findAll();
        $levels = $levelModel->findAll();
        $progress = $progressModel->findAll();

        require __DIR__ . '/../views/admin/test.php';
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

    public function testCreateLevel()
{
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: /login');
        exit;
    }

    $levelModel = new Level($this->db);

    $levelModel->create();

    header('Location: /admin/test?success=1');
    exit;
}
}