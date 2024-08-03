<?php
declare(strict_types=1);

use App\RequestValidators\RequestValidatorFactory;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;

return [
    App::class => function(ContainerInterface $container) {
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        // TODO: add routes and middlewares
        $routes = require CONFIGS_PATH . '/routes.php';
        $routes($app);

        $middlewares = require CONFIGS_PATH . '/middlewares.php';
        $middlewares($app);

        return $app;
    },
    Twig::class => function() {
        $twig = Twig::create(
            path: VIEWS_PATH,
            settings: [
                'cache' => STORAGE_PATH . "/cache",
                'auto_reload' => $_ENV['APP_ENVIRONMENT'] == 'development'
            ]
        );

        // add any twig extensions here

        return $twig;
    },
    EntityManager::class => function() { // TODO: abstract env access away
        $conn = DriverManager::getConnection([
                'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
                'host' => $_ENV['DB_HOST'],
                'dbname' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASS'],
        ]);

        $ormSetup = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . "/../../app/Entities/"],
            isDevMode: true
        );

        return new EntityManager($conn, $ormSetup);
    },
    RequestValidatorFactory::class => function(ContainerInterface $container) {
        return new RequestValidatorFactory($container);
    },
    ResponseFactoryInterface::class => function(ContainerInterface $container) {
        /** @var App $app */
        $app = $container->get(App::class);

        return $app->getResponseFactory();
    }
];