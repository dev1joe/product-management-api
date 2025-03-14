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
        $group->group('/products', function(RouteCollectorProxy $products) {
            $products->get('', [ProductController::class, 'fetchAllPaginated']);
            $products->get('/{id:[0-9]+}', [ProductController::class, 'fetchById']);
            $products->delete('/{id:[0-9]+}', [ProductController::class, 'delete']);
            $products->post('', [ProductController::class, 'create']);
            $products->post('/{id:[0-9]+}', [ProductController::class, 'update']);
        });

        //[___________________________ categories ___________________________]
        $group->group('/categories', function(RouteCollectorProxy $categories) {
            $categories->get('', [CategoryController::class, 'fetchAllPaginated']);
            $categories->get('/{id:[0-9]+}', [CategoryController::class, 'fetchById']);
            $categories->get('/names', [CategoryController::class, 'fetchNames']);
            $categories->delete('/{id:[0-9]+}', [CategoryController::class, 'delete']);
            $categories->post('', [CategoryController::class, 'create']);
            $categories->post('/{id:[0-9]+}', [CategoryController::class, 'update']);
        });

        //[___________________________ manufacturers ___________________________]
        $group->group('/manufacturers', function(RouteCollectorProxy $manufacturers) {
            $manufacturers->get('', [ManufacturerController::class, 'fetchAllPaginated']);
            $manufacturers->get('/{id:[0-9]+}', [ManufacturerController::class, 'fetchById']);
            $manufacturers->get('/names', [ManufacturerController::class, 'fetchNames']);
            $manufacturers->delete('/{id:[0-9]+}', [ManufacturerController::class, 'delete']);
            $manufacturers->post('', [ManufacturerController::class, 'create']);
            $manufacturers->post('/{id:[0-9]+}', [ManufacturerController::class, 'update']);
        });

        //[___________________________ warehouses ___________________________]
        $group->group('/warehouses', function(RouteCollectorProxy $warehouses) {
            $warehouses->get('', [WarehouseController::class, 'fetchAllPaginated']);
            $warehouses->get('/{id:[0-9]+}', [WarehouseController::class, 'fetchById']);
            $warehouses->delete('/{id:[0-9]+}', [WarehouseController::class, 'delete']);
            $warehouses->post('', [WarehouseController::class, 'create']);
            $warehouses->post('/{id:[0-9]+}', [WarehouseController::class, 'update']);
        });

        //[___________________________ Addresses ___________________________]
        $group->group('/addresses', function(RouteCollectorProxy $addresses) {
            $addresses->get('', [AddressController::class, 'fetchAllPaginated']);
            $addresses->get('/{id:[0-9]+}', [AddressController::class, 'fetchById']);
            $addresses->delete('/{id:[0-9]+}', [AddressController::class, 'delete']);
            $addresses->post('', [AddressController::class, 'create']);
            $addresses->post('/{id:[0-9]+}', [AddressController::class, 'update']);
        });

        //[___________________________ Inventory ___________________________]
        $group->group('/inventories', function (RouteCollectorProxy $inventories) {
            $inventories->get('', [InventoryController::class, 'fetchAllPaginated']);
            $inventories->get('/{id:[0-9]+}', [InventoryController::class, 'fetchById']);
            $inventories->delete('/{id:[0-9]+}', [InventoryController::class, 'delete']);
            $inventories->post('', [InventoryController::class, 'create']);
            $inventories->post('/{id:[0-9]+}', [InventoryController::class, 'update']);
        });

        //[___________________________ Customers ___________________________]
        $group->group('/customers', function(RouteCollectorProxy $customers) {
            $customers->get('', [CustomerController::class, 'fetchAll']);
            $customers->get('/{id:[0-9]+}', [CustomerController::class, 'fetchById']);
        });
        // $group->get('/customers/{email:.+}', [CustomerController::class, 'fetchByEmail']);

    })->add(CorsMiddleware::class);

    // TODO: Error handling is not working with wrong routes
    //->add(AjaxHandleExceptionsMiddleware::class);
};