<?php
declare(strict_types=1);

use App\Controllers\AddressController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\CustomerController;
use App\Controllers\FileController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\WarehouseController;
use App\Middlewares\CustomerAuthorizationMiddleware;
use App\Middlewares\GuestMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    $app->get('/', [HomeController::class, 'index']);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/login', [AuthController::class, 'loginForm']);
        $group->post('/login', [AuthController::class, 'logIn']);
        $group->get('/register', [AuthController::class, 'registrationForm']);
        $group->post('/register', [AuthController::class, 'register']);
    })->add(GuestMiddleware::class);

    // $app->get('/profile', [CustomerController::class, 'profile'])->add(AuthMiddleware::class);

    //TODO: define routes group that will have the "customer authentication" middleware
    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/profile', [CustomerController::class, 'profile']);
        $group->get('/cart', []);
        $group->get('/wishlist', []);
        $group->post('/logout', [AuthController::class, 'logOut']);
    })->add(CustomerAuthorizationMiddleware::class);


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

       $group->get('/warehouse/update/{id}', [WarehouseController::class, 'updateForm']);

       $group->post('/warehouse', [WarehouseController::class, 'create']);
       $group->post('/warehouse/{id}', [WarehouseController::class, 'update']);

       $group->delete('/warehouse/{id}', [WarehouseController::class, 'delete']);

        // [________________________________________ address ________________________________________]
       $group->get('/addresses', [AddressController::class, 'fetchAll']);


        // [________________________________________ order ________________________________________]
       $group->get('/orders', [OrderController::class, 'fetchAll']);

        // [________________________________________ customer ________________________________________]
       $group->get('/customer/all', [CustomerController::class, 'fetchAll']);

        // [________________________________________ files ________________________________________]
        $group->get('/upload/file', [FileController::class, 'form']);
       $group->post('/upload/file', [FileController::class, 'store']);

    }); // TODO: admin authentication middleware should be associated with this group
};

