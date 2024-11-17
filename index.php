<?php

require_once 'autoload.php';

use App\Core\Application;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CorsMiddleware;

$app = Application::getInstance();

$router = $app->getRouter();

$router->middleware(CorsMiddleware::class);

$router->post('/auth/signup', [AuthController::class, 'signup']);
$router->post('/auth/signin', [AuthController::class, 'signin']);

$router->resolve();