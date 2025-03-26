<?php
declare(strict_types=1);

use App\Middlewares\AuthenticateMiddleware;
use App\Middlewares\CorsMiddleware;
use Slim\App;

return function(App $app) {
    // $app->add(AuthenticateMiddleware::class);
    $app->add(CorsMiddleware::class);
};