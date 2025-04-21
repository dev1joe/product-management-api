<?php
declare(strict_types = 1);

use Doctrine\ORM\EntityManager;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Psr\Container\ContainerInterface;

$config = new PhpFile('migrations.php'); // Or use one of the Doctrine\Migrations\Configuration\Configuration\* loaders

/** @var ContainerInterface $container */
$container = require 'bootstrap.php';

$entityManager = $container->get(EntityManager::class);

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));