<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;

// import path constants
require 'configs/path_constants.php';

// activate auto loading
require __DIR__ . '/vendor/autoload.php';

// environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// make container
/** @var ContainerInterface $container */
$container = require CONFIGS_PATH . '/Container/container.php';
return $container;