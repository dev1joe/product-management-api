<?php
declare(strict_types=1);

use App\Controllers\AddressController;
use App\Controllers\CategoryController;
use App\Controllers\CustomerController;
use App\Controllers\FileController;
use App\Controllers\InventoryController;
use App\Controllers\ManufacturerController;
use App\Controllers\ProductController;
use App\Controllers\WarehouseController;
use App\Middlewares\AjaxHandleExceptionsMiddleware;
use App\Middlewares\CorsMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// RESTful APIs use path parameters
return function(App $app) {
    // TODO: all js fetch requests should be requesting api routes
    // TODO: exception handling

    $app->group('/api', function(RouteCollectorProxy $group) {
        $group->get('/files/{file:.+}', [FileController::class, 'fetchFile']);
        $group->get('/files', [FileController::class, 'tmp']);

        //[___________________________ products ___________________________]
        $group->get('/products', [ProductController::class, 'fetchAllPaginated']);
        $group->get('/products/{id:[0-9]+}', [ProductController::class, 'fetchById']);

        $group->post('/products', [ProductController::class, 'create']);
        $group->post('/products/{id:[0-9]+}', [ProductController::class, 'update']);
        $group->delete('/products/{id:[0-9]+}', [ProductController::class, 'delete']);

        //[___________________________ categories ___________________________]
        $group->get('/categories', [CategoryController::class, 'fetchAllPaginated']);
        $group->get('/categories/{id:[0-9]+}', [CategoryController::class, 'fetchById']);
        $group->get('/categories/names', [CategoryController::class, 'fetchNames']);
        $group->post('/categories', [CategoryController::class, 'create']);
        $group->post('/categories/{id:[0-9]+}', [CategoryController::class, 'update']);
        $group->delete('/categories/{id:[0-9]+}', [CategoryController::class, 'delete']);

        //[___________________________ manufacturers ___________________________]
        $group->get('/manufacturers', [ManufacturerController::class, 'fetchAllPaginated']);
        $group->get('/manufacturers/{id:[0-9]+}', [ManufacturerController::class, 'fetchById']);
        $group->get('/manufacturers/names', [ManufacturerController::class, 'fetchNames']);

        //[___________________________ warehouses ___________________________]
        $group->get('/warehouses', [WarehouseController::class, 'fetchAll']);
        $group->get('/warehouses/{id:[0-9]+}', [WarehouseController::class, 'fetchById']);

        //[___________________________ Addresses ___________________________]
        $group->get('/addresses', [AddressController::class, 'fetchAll']);
        $group->get('/addresses/{id:[0-9]+}', [AddressController::class, 'fetchById']);

        //[___________________________ Customers ___________________________]
        $group->get('/customers', [CustomerController::class, 'fetchAll']);
        $group->get('/customers/{id:[0-9]+}', [CustomerController::class, 'fetchById']);
//        $group->get('/customers/{email:.+}', [CustomerController::class, 'fetchByEmail']);

        //[___________________________ Inventory ___________________________]
        $group->get('/inventory', [InventoryController::class, 'fetchAll']);
        $group->get('/inventory/{id:[0-9]+}', [InventoryController::class, 'fetchById']);

    })->add(CorsMiddleware::class);

    // TODO: Error handling is not working with wrong routes
    //->add(AjaxHandleExceptionsMiddleware::class);
};