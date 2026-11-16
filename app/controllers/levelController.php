
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

        $userProgressData = $userId
            ? $this->progress->findByUser($userId)
            : [];

        $userProgress = [];

        foreach ($userProgressData as $p) {
            $userProgress[$p['level_id']] = $p;
        }

        require '../app/views/levels/index.php';
    }
}
