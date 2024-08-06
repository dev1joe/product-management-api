<?php
declare(strict_types=1);

use App\Controllers\AddressController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\WarehouseController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    $app->get('/', [HomeController::class, 'index']);

    $app->group('/admin', function(RouteCollectorProxy $group) {
       $group->get('/products', [ProductController::class, 'fetchAll']);
       $group->get('/create/product', [ProductController::class, 'form']);
       $group->post('/product', [ProductController::class, 'create']);
       $group->post('/product/{id}', [ProductController::class, 'update']);

       $group->get('/categories', [CategoryController::class, 'fetchAll']);
       $group->get('/create/category', [CategoryController::class, 'form']);
       $group->post('/category', [CategoryController::class, 'create']);
       $group->post('/category/{id}', [CategoryController::class, 'update']);

       $group->get('/warehouses', [WarehouseController::class, 'fetchAll']);
       $group->get('/create/warehouse', [WarehouseController::class, 'form']);
       $group->post('/warehouse', [WarehouseController::class, 'create']);
       $group->post('/warehouse/{id}', [WarehouseController::class, 'update']);

       $group->get('/addresses', [AddressController::class, 'fetchAll']);

       $group->get('/orders', [OrderController::class, 'fetchAll']);

       $group->get('/customers', [CustomerController::class, 'fetchAll']);

    }); // authentication middleware should be associated with this group
};

