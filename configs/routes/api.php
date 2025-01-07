<?php
declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\FileController;
use App\Controllers\ManufacturerController;
use App\Controllers\ProductController;
use App\Middlewares\AjaxHandleExceptionsMiddleware;
use App\Middlewares\CorsMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    // TODO: all js fetch requests should be requesting api routes
    // TODO: exception handling

    $app->group('/api', function(RouteCollectorProxy $group) {
        $group->get('/files/{file:.+}', [FileController::class, 'fetchFile']);
        $group->get('/files', [FileController::class, 'tmp']);

        //[___________________________ products ___________________________]
        $group->get('/products', [ProductController::class, 'fetchAllPaginated']);
        $group->get('/products/{id}', [ProductController::class, 'fetchById']);

        $group->post('/products', [ProductController::class, 'create']);
        $group->post('/products/{id}', [ProductController::class, 'update']);
        $group->delete('/products/{id}', [ProductController::class, 'delete']);

        //[___________________________ categories ___________________________]
        //TODO: a route for fetching all categories with all data and a router for all categories but only names and ids
        $group->get('/categories', [CategoryController::class, 'fetchAllPaginated']);
        $group->get('/categories/names', [CategoryController::class, 'fetchNames']);
        $group->post('/categories', [CategoryController::class, 'create']);
        $group->post('/categories/{id}', [CategoryController::class, 'update']);
        $group->delete('/categories/{id}', [CategoryController::class, 'delete']);

        //[___________________________ manufacturers ___________________________]
        $group->get('/manufacturers/names', [ManufacturerController::class, 'fetchNames']);

    })->add(AjaxHandleExceptionsMiddleware::class)->add(CorsMiddleware::class);
};