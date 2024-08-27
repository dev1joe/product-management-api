<?php
declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Middlewares\AjaxValidationExceptionMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    //TODO: all js fetch requests should be requesting api routes

    $app->group('/api', function(RouteCollectorProxy $group) {
        $group->get('/products', [ProductController::class, 'fetchAllPaginated']);
        $group->get('/categories', [CategoryController::class, 'fetchAll']);

        $group->post('/categories', [CategoryController::class, 'create']);
    })->add(AjaxValidationExceptionMiddleware::class);
};