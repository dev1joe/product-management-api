<?php
declare(strict_types=1);
ini_set('display_errors', true);

use Psr\Container\ContainerInterface;
use Slim\App;

// activate auto loading
require __DIR__ . '/../vendor/autoload.php';

// environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// make container
/** @var ContainerInterface $container */
$container = require '../configs/Container/container.php';

// initialize app
/** @var App $app */
$app = $container->get(App::class);

// run app
$app->run();

/******* sandbox ********/
$entityManager = $container->get(\Doctrine\ORM\EntityManager::class);