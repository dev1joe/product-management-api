<?php
declare(strict_types=1);

use App\Middlewares\CorsMiddleware;
use App\Middlewares\JsonMiddleware;
use Slim\App;

return function(App $app) {
    $app->add(CorsMiddleware::class);
    $app->add(JsonMiddleware::class);
};