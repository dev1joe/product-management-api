<?php
declare(strict_types=1);
ini_set('display_errors', true);

use Psr\Container\ContainerInterface;
use Slim\App;


/** @var ContainerInterface $container */
$container = require __DIR__ . '/../bootstrap.php';

// initialize app
/** @var App $app */
$app = $container->get(App::class);

// run app
$app->run();

/******* sandbox ********/
// $entityManager = $container->get(\Doctrine\ORM\EntityManager::class);