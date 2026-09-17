<?php

session_start();

require_once '../config/database.php';

require_once '../app/models/User.php';
require_once '../app/models/Level.php';
require_once '../app/models/Progress.php';

require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/GameController.php';
require_once '../app/controllers/AdminController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/LevelController.php';
require_once '../app/controllers/ProgressController.php';

require_once 'routes.php';