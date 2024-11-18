<?php

require_once 'autoload.php';

use App\Core\Application;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Middleware\CorsMiddleware;

$app = Application::getInstance();

$router = $app->getRouter();

$router->middleware(CorsMiddleware::class);

$router->post('/api/auth/signup', [AuthController::class, 'signup']);
$router->post('/api/auth/signin', [AuthController::class, 'signin']);
$router->delete('/api/auth/logout', [AuthController::class, 'logout']);

$router->get('/api/recipes', [RecipeController::class, 'getRecipeByRecipeId']);
$router->get('/api/recipes/all', [RecipeController::class, 'index']);
$router->post('/api/recipes/create', [RecipeController::class, 'create']);
$router->put('/api/recipes/update', [RecipeController::class, 'update']);

$router->resolve();