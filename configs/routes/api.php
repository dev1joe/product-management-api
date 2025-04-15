<?php
declare(strict_types=1);

use App\Controllers\AddressController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\InventoryController;
use App\Controllers\ManufacturerController;
use App\Controllers\ProductController;
use App\Controllers\WarehouseController;
use App\Middlewares\AuthMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    $app->group('/api', function(RouteCollectorProxy $group) {
        $group->group('/v1', function(RouteCollectorProxy $v1) {
            $v1->post('/login', [AuthController::class, 'getToken']);

            //[___________________________ products ___________________________]
            $v1->get('/products', [ProductController::class, 'fetchAllPaginated']);
            $v1->get('/products/{id:[0-9]+}', [ProductController::class, 'fetchById']);

            $v1->group('/products', function(RouteCollectorProxy $products) {
                $products->delete('/{id:[0-9]+}', [ProductController::class, 'delete']);
                $products->post('', [ProductController::class, 'create']);
                $products->patch('/{id:[0-9]+}', [ProductController::class, 'update']);
            })->add(AuthMiddleware::class);

            //[___________________________ categories ___________________________]
            $v1->get('/categories', [CategoryController::class, 'fetchAllPaginated']);
            $v1->get('/categories/{id:[0-9]+}', [CategoryController::class, 'fetchById']);
            $v1->get('/categories/names', [CategoryController::class, 'fetchNames']);

            $v1->group('/categories', function(RouteCollectorProxy $categories) {
                $categories->delete('/{id:[0-9]+}', [CategoryController::class, 'delete']);
                $categories->post('', [CategoryController::class, 'create']);
                $categories->patch('/{id:[0-9]+}', [CategoryController::class, 'update']);
            })->add(AuthMiddleware::class);

            //[___________________________ manufacturers ___________________________]
            $v1->get('/manufacturers', [ManufacturerController::class, 'fetchAllPaginated']);
            $v1->get('/manufacturers/{id:[0-9]+}', [ManufacturerController::class, 'fetchById']);
            $v1->get('/manufacturers/names', [ManufacturerController::class, 'fetchNames']);

            $v1->group('/manufacturers', function(RouteCollectorProxy $manufacturers) {
                $manufacturers->delete('/{id:[0-9]+}', [ManufacturerController::class, 'delete']);
                $manufacturers->post('', [ManufacturerController::class, 'create']);
                $manufacturers->patch('/{id:[0-9]+}', [ManufacturerController::class, 'update']);
            })->add(AuthMiddleware::class);

            //[___________________________ warehouses ___________________________]
            $v1->get('/warehouses', [WarehouseController::class, 'fetchAllPaginated']);
            $v1->get('/warehouses/{id:[0-9]+}', [WarehouseController::class, 'fetchById']);

            $v1->group('/warehouses', function(RouteCollectorProxy $warehouses) {
                $warehouses->delete('/{id:[0-9]+}', [WarehouseController::class, 'delete']);
                $warehouses->post('', [WarehouseController::class, 'create']);
                $warehouses->patch('/{id:[0-9]+}', [WarehouseController::class, 'update']);
            })->add(AuthMiddleware::class);

            //[___________________________ Addresses ___________________________]
            $v1->get('/addresses', [AddressController::class, 'fetchAllPaginated']);
            $v1->get('/addresses/{id:[0-9]+}', [AddressController::class, 'fetchById']);

            $v1->group('/addresses', function(RouteCollectorProxy $addresses) {
                $addresses->delete('/{id:[0-9]+}', [AddressController::class, 'delete']);
                $addresses->post('', [AddressController::class, 'create']);
                $addresses->patch('/{id:[0-9]+}', [AddressController::class, 'update']);
            })->add(AuthMiddleware::class);

            //[___________________________ Inventory ___________________________]
            $v1->get('/inventories', [InventoryController::class, 'fetchAllPaginated']);
            $v1->get('/inventories/{id:[0-9]+}', [InventoryController::class, 'fetchById']);

            $v1->group('/inventories', function (RouteCollectorProxy $inventories) {
                $inventories->delete('/{id:[0-9]+}', [InventoryController::class, 'delete']);
                $inventories->post('', [InventoryController::class, 'create']);
                $inventories->patch('/{id:[0-9]+}', [InventoryController::class, 'update']);
            })->add(AuthMiddleware::class);

            $v1->group('/test', function(RouteCollectorProxy $test) {
                $test->get('', function() {
                     throw new Exception("Something Went Wrong!"); // simulate an error
                });
            });
        });

        // implement v2 here
        // $group->group('/v2', function(RouteCollectorProxy $v2) {});
    });
};