<?php
declare(strict_types=1);

use App\Middlewares\ValidationExceptionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function(App $app) {
    $container = $app->getContainer();

    // twig middleware
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
    $app->add(ValidationExceptionMiddleware::class);
};