<?php
declare(strict_types=1);

use App\Middlewares\ValidationExceptionMiddleware;
use Slim\App;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function(App $app) {
    $container = $app->getContainer();

    $app->add(MethodOverrideMiddleware::class);
    // twig middleware
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
    $app->add(ValidationExceptionMiddleware::class);
};