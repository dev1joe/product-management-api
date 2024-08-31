<?php
declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Middlewares\AjaxHandleExceptionsMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    //TODO: all js fetch requests should be requesting api routes

    $app->group('/api', function(RouteCollectorProxy $group) {
        //[___________________________ products ___________________________]
        $group->get('/products', [ProductController::class, 'fetchAllPaginated']);
        $group->get('/products/{id}', [ProductController::class, 'fetchById']);

        $group->post('/products', [ProductController::class, 'create']);
        $group->post('/products/{id}', [ProductController::class, 'update']);
        $group->delete('/products/{id}', [ProductController::class, 'delete']);

        //[___________________________ categories ___________________________]
        $group->get('/categories', [CategoryController::class, 'fetchAllPaginated']);
        $group->post('/categories', [CategoryController::class, 'create']);
        $group->delete('/categories/{id}', [CategoryController::class, 'delete']);
    })->add(AjaxHandleExceptionsMiddleware::class);
};