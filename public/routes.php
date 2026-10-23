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
        (new GameController($db))->infinite();
        break;

    case $path === '/levels':
        (new LevelController($db))->index();
        break;

    // ADMIN

    case $path === '/admin':
        (new AdminController($db))->index();
        break;


    // TESTE DO BANCO

    case $path === '/admin/test':
        (new AdminController($db))->test();
        break;

    case $path === '/admin/test/user/create' && $method === 'POST':
        (new AdminController($db))->testCreateUser();
        break;

    case $path === '/admin/test/level/create' && $method === 'POST':
        (new AdminController($db))->testCreateLevel();
        break;


    // ADMIN USERS

    case $path === '/admin/users':
        (new UserController($db))->index();
        break;

    case $path === '/admin/users/create':
        (new UserController($db))->create();
        break;

    case $path === '/admin/users/edit':
        (new UserController($db))->edit();
        break;

    case $path === '/admin/users/delete' && $method === 'POST':
        (new UserController($db))->delete();
        break;


    // ADMIN LEVELS

    case $path === '/admin/levels':
        (new LevelController($db))->index();
        break;

    case $path === '/admin/levels/create':
        (new LevelController($db))->create();
        break;

    case $path === '/admin/levels/delete' && $method === 'POST':
        (new LevelController($db))->delete();
        break;


    // ADMIN PROGRESS

    case $path === '/admin/progress':
        (new ProgressController($db))->index();
        break;

    case $path === '/admin/progress/edit':
        (new ProgressController($db))->edit();
        break;

    case $path === '/admin/progress/delete' && $method === 'POST':
        (new ProgressController($db))->delete();
        break;

    // VISUAL NOVEL
    case $path === '/vn/scene':
        (new VnController($db))->scene();
        break;

    case $path === '/vn/save' && $method === 'POST':
        (new VnController($db))->save();
        break;

    case $path === '/vn/assets':
        (new VnController($db))->assets();
        break;

    case $path === '/vn/upload' && $method === 'POST':
        (new VnController($db))->upload();
        break;

    default:
    include __DIR__ . '/../app/views/home/home.php';
    break;
}