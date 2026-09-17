<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch (true) {

    // AUTH
    case $uri === '/login':
        (new AuthController($db))->login();
        break;

    case $uri === '/register':
        (new AuthController($db))->register();
        break;

    case $uri === '/logout':
        (new AuthController($db))->logout();
        break;


    // JOGO
    case $uri === '/game':
        (new GameController($db))->show();
        break;

    case $uri === '/game/complete' && $method === 'POST':
        (new GameController($db))->complete();
        break;


    // ADMIN
    case $uri === '/admin':
        (new AdminController($db))->index();
        break;


    // ADMIN - USERS
    case $uri === '/admin/users':
        (new UserController($db))->index();
        break;

    case $uri === '/admin/users/create':
        (new UserController($db))->create();
        break;

    case $uri === '/admin/users/edit':
        (new UserController($db))->edit();
        break;

    case $uri === '/admin/users/delete' && $method === 'POST':
        (new UserController($db))->delete();
        break;


    // ADMIN - LEVELS
    case $uri === '/admin/levels':
        (new LevelController($db))->index();
        break;

    case $uri === '/admin/levels/create':
        (new LevelController($db))->create();
        break;

    case $uri === '/admin/levels/delete' && $method === 'POST':
        (new LevelController($db))->delete();
        break;


    // ADMIN - PROGRESS
    case $uri === '/admin/progress':
        (new ProgressController($db))->index();
        break;

    case $uri === '/admin/progress/edit':
        (new ProgressController($db))->edit();
        break;

    case $uri === '/admin/progress/delete' && $method === 'POST':
        (new ProgressController($db))->delete();
        break;


    default:
        http_response_code(404);
        echo 'Página não encontrada.';
}