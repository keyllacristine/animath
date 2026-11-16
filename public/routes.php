<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$basePath = '/animath/public';
define('BASE_URL', $basePath);

if (str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath));
}

if ($path === '') {
    $path = '/';
}

switch (true) {

    // AUTH

    case $path === '/login':
        (new AuthController($db))->login();
        break;

    case $path === '/register':
        (new AuthController($db))->register();
        break;

    case $path === '/logout':
        (new AuthController($db))->logout();
        break;

    // PERFIL
    case $path === '/profile':
        (new ProfileController($db))->index();
        break;

    // HOME

    case $path === '/home':
        include __DIR__ . '/../app/views/home/home.php';
        break;

    // CONFIGURAÇOES

    case $path === '/settings':
        require_once __DIR__ . '/../app/views/settings/index.php';
        break;

    // DESENVOLVEDORES

    case $path === '/developers':
        require_once __DIR__ . '/../app/views/settings/developers.php';
        break;

    // GAME

    case $path === '/game':
        (new GameController($db))->show();
        break;

    case $path === '/game/complete' && $method === 'POST':
        (new GameController($db))->complete();
        break;

    case $path === '/game/infinite':
    case $path === '/game/arcade':
        (new GameController($db))->arcade();
        break;

    case str_starts_with($path, '/game/arcade/'):
        (new GameController($db))->arcadeGame(substr($path, strlen('/game/arcade/')));
        break;

    case $path === '/levels':
        (new LevelController($db))->index();
        break;

    
    // VISUAL NOVEL
    case $path === '/vn/scene':
        (new VnController($db))->scene();
        break;

    default:
    include __DIR__ . '/../app/views/home/home.php';
    break;
}