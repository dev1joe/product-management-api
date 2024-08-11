<?php
declare(strict_types=1);

use App\Controllers\AddressController;
use App\Controllers\CategoryController;
use App\Controllers\FileController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\WarehouseController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    $app->get('/', [HomeController::class, 'index']);

    $app->group('/admin', function(RouteCollectorProxy $group) {
        // [________________________________________ product ________________________________________]
       $group->get('/product/all', [ProductController::class, 'fetchAll']);
       $group->get('/product/create', [ProductController::class, 'form']);

       $group->post('/product', [ProductController::class, 'create']);
       $group->post('/product/{id}', [ProductController::class, 'update']);

       $group->delete('/product/{id}', [ProductController::class, 'delete']);

        // [________________________________________ category ________________________________________]
       $group->get('/category/create', [CategoryController::class, 'form']);
       $group->get('/category/all', [CategoryController::class, 'fetchAll']);
       $group->get('/category/{id}', [CategoryController::class, 'fetchById']);

       $group->post('/category', [CategoryController::class, 'create']);
       $group->post('/category/{id}', [CategoryController::class, 'update']);

       $group->delete('/category/{id}', [CategoryController::class, 'delete']);

       // [________________________________________ warehouse ________________________________________]
       $group->get('/warehouse/create', [WarehouseController::class, 'form']);
       $group->get('/warehouse/all', [WarehouseController::class, 'fetchAll']);
       $group->get('/warehouse/{id}', [WarehouseController::class, 'fetchById']);

       $group->post('/warehouse', [WarehouseController::class, 'create']);
       $group->post('/warehouse/{id}', [WarehouseController::class, 'update']);

       $group->delete('/warehouse/{id}', [WarehouseController::class, 'delete']);

        // [________________________________________ address ________________________________________]
       $group->get('/addresses', [AddressController::class, 'fetchAll']);


        // [________________________________________ order ________________________________________]
       $group->get('/orders', [OrderController::class, 'fetchAll']);

        // [________________________________________ customer ________________________________________]
       $group->get('/customers', [CustomerController::class, 'fetchAll']);

        // [________________________________________ files ________________________________________]
        $group->get('/upload/file', [FileController::class, 'form']);
       $group->post('/upload/file', [FileController::class, 'store']);

    }); // authentication middleware should be associated with this group
};

