<?php

use App\Controllers\HomeController;
use App\Middleware\AuthMiddleware;
use App\Middleware\LoggingMiddleware;
use Illuminate\Routing\Router;

/** @var Router $router */
$router = app('router');

// 1. Basic Route using Array Controller Resolution [HomeController::class, 'index']
$router->get('/', [HomeController::class, 'index'])
    ->middleware([LoggingMiddleware::class]);

// 2. Closure Route
$router->get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'framework' => 'Laralite',
        'timestamp' => time()
    ]);
});

// 3. Route Group with Prefix & Group Middleware
$router->group([
    'prefix' => 'api',
    'middleware' => [LoggingMiddleware::class, AuthMiddleware::class]
], function (Router $router) {
    $router->get('/users', [HomeController::class, 'users']);
    
    $router->get('/info', function () {
        return response()->json(['service' => 'Laralite API v1.0']);
    });
});
