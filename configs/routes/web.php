<?php
declare(strict_types=1);

use App\Controllers\AddressController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\CustomerController;
use App\Controllers\FileController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\WarehouseController;
use App\Middlewares\AdminAuthorizationMiddleware;
use App\Middlewares\CustomerAuthorizationMiddleware;
use App\Middlewares\GuestMiddleware;
use App\Middlewares\ProfileMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function(App $app) {
    $app->get('/session', [HomeController::class, 'showSession']); // for testing purposes

    $app->get('/', [HomeController::class, 'index']);
    $app->get('/products', [HomeController::class, 'products']);
    $app->get('/products/{id}', [ProductController::class, 'productPage']);
    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/login', [AuthController::class, 'loginForm']);
        $group->post('/login', [AuthController::class, 'logIn']);
        $group->get('/register', [AuthController::class, 'registrationForm']);
        $group->post('/register', [AuthController::class, 'register']);
        //-------------
        $group->get('/admin/login', [AuthController::class, 'adminLoginForm']);
    })->add(GuestMiddleware::class);

    // $app->get('/profile', [CustomerController::class, 'profile'])->add(AuthMiddleware::class);

    $app->get('/profile', [CustomerController::class, 'profile'])->add(ProfileMiddleware::class);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/cart', []);
        $group->get('/wishlist', [CustomerController::class, 'wishlist']);
        $group->post('/logout', [AuthController::class, 'logOut']);
    })->add(CustomerAuthorizationMiddleware::class);


    $app->group('/admin', function(RouteCollectorProxy $group) {
        $group->get('', function(Request $request, Response $response) {
            return $response
                ->withHeader('Location', '/admin/dashboard')
                ->withStatus(302);
        });
        $group->get('/dashboard', [AdminController::class, 'dashboardView']);
        $group->post('/view', [AdminController::class, 'viewRequest']);


        // [________________________________________ product ________________________________________]
       $group->get('/products', [AdminController::class, 'productsView']);
       $group->get('/products/create', [ProductController::class, 'form']);

       $group->post('/products', [ProductController::class, 'create']);
       $group->post('/products/{id}', [ProductController::class, 'update']);

       $group->delete('/products/{id}', [ProductController::class, 'delete']);

        // [________________________________________ category ________________________________________]
       $group->get('/categories', [AdminController::class, 'categoryView']);
       $group->get('/categories/create', [CategoryController::class, 'form']);
       $group->get('/categories/{id}', [CategoryController::class, 'fetchById']);

       $group->post('/categories', [CategoryController::class, 'create']);
       $group->post('/categories/{id}', [CategoryController::class, 'update']);

       $group->delete('/categories/{id}', [CategoryController::class, 'delete']);

       // [________________________________________ warehouse ________________________________________]
       $group->get('/warehouses/create', [WarehouseController::class, 'form']);
       $group->get('/warehouses', [WarehouseController::class, 'fetchAllPaginated']);
       $group->get('/warehouses/{id}', [WarehouseController::class, 'fetchById']);

       $group->get('/warehouses/update/{id}', [WarehouseController::class, 'updateForm']);

       $group->post('/warehouses', [WarehouseController::class, 'create']);
       $group->post('/warehouses/{id}', [WarehouseController::class, 'update']);

       $group->delete('/warehouses/{id}', [WarehouseController::class, 'delete']);

        // [________________________________________ address ________________________________________]
       $group->get('/addresses', [AddressController::class, 'fetchAll']);


        // [________________________________________ order ________________________________________]
       $group->get('/orders', [OrderController::class, 'fetchAll']);

        // [________________________________________ customer ________________________________________]
       $group->get('/customers', [CustomerController::class, 'fetchAll']);

        // [________________________________________ files ________________________________________]
        $group->get('/upload/file', [FileController::class, 'form']);
       $group->post('/upload/file', [FileController::class, 'store']);

    })->add(AdminAuthorizationMiddleware::class);
};

