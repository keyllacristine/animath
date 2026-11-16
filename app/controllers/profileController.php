<?php

class ProfileController
{
    private User $user;
    private Progress $progress;

    public function __construct(PDO $db)
    {
        $this->user = new User($db);
        $this->progress = new Progress($db);
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? $_SESSION['username'];
            $avatar = (int) ($_POST['avatar'] ?? $_SESSION['avatar']);

            if ($this->user->update($userId, $username, $avatar)) {
                $_SESSION['username'] = $username;
                $_SESSION['avatar'] = $avatar;
            }

            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        $userData = $this->user->findById($userId);

        $progressData = $this->progress->findByUser($userId);

        $totalHits = 0;
        $totalErrors = 0;

        foreach ($progressData as $item) {
            $totalHits += (int) $item['hits'];
            $totalErrors += (int) $item['errors'];
        }

        require '../app/views/auth/profile.php';
    }
}