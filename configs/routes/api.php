<?php
declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Middlewares\AjaxValidationExceptionMiddleware;
use Slim\App;

return function(App $app) {
    //TODO: all js fetch requests should be requesting api routes
    $app->post('/api/categories', [CategoryController::class, 'create'])->add(AjaxValidationExceptionMiddleware::class);
};