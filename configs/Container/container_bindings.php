<?php
declare(strict_types=1);

use App\Config;
use App\Contracts\AuthServiceInterface;
use App\Enums\StorageDriver;
use App\ErrorHandler;
use App\EventListeners\ProductListener;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\AuthService;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use function DI\create;

return [
    App::class => function(ContainerInterface $container) {
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        //adding web routes
        $routes = require CONFIGS_PATH . '/routes/web.php';
        $routes($app);

        // add api routes
        $api = require CONFIGS_PATH . '/routes/api.php';
        $api($app);

        // add middlewares
        $middlewares = require CONFIGS_PATH . '/middlewares.php';
        $middlewares($app);

        $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler(ErrorHandler::class);

        return $app;
    },
    Config::class => create(Config::class)->constructor(
        require CONFIGS_PATH . '/app.php'
    ),
    EntityManager::class => function(Config $config) {
        $conn = DriverManager::getConnection($config->get('doctrine.connection'));

        $ormSetup = ORMSetup::createAttributeMetadataConfiguration(
            paths: [ENTITIES_PATH],
            isDevMode: $config->get('app_env') == 'development'
        );

        // Event manager //TODO: event listeners vs event subscribers
        $eventManager = new EventManager();

        $productListener = new ProductListener();
        $eventManager->addEventSubscriber($productListener);

        return new EntityManager($conn, $ormSetup, $eventManager);
    },
    RequestValidatorFactory::class => function(ContainerInterface $container) {
        return new RequestValidatorFactory($container);
    },
    ResponseFactoryInterface::class => function(ContainerInterface $container) {
        /** @var App $app */
        $app = $container->get(App::class);

        return $app->getResponseFactory();
    },
    Filesystem::class => function(Config $config) {
        // The internal adapter
        $adapter = match($config->get('storage.driver')) {
            StorageDriver::Local => new League\Flysystem\Local\LocalFilesystemAdapter(
                PRODUCT_STORAGE_PATH
            ),
            //TODO: add FTP driver here if needed
        };

        // The FilesystemOperator
        return new League\Flysystem\Filesystem($adapter);
    },
//    AuthServiceInterface::class => function(ContainerInterface $container) {
//        return $container->get(AuthService::class);
//        // `App\Services\AuthService` is the default implementation of the AuthServiceInterface
//    },
];