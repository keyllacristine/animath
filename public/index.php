<?php

session_start();

require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Level.php';
require_once __DIR__ . '/../app/models/Progress.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/GameController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/LevelController.php';
require_once __DIR__ . '/../app/controllers/ProgressController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';

require_once __DIR__ . '/routes.php';