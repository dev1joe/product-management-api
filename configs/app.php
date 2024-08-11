<?php
declare(strict_types=1);

use App\Enums\AppEnvironment;
use App\Enums\StorageDriver;

$appEnv = $_ENV['APP_ENV'] ?? AppEnvironment::Production;

return [
    'app_name' => $_ENV['APP_NAME'],
    'app_env' => $appEnv,
    'doctrine' => [
        'connection' => [
            'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
        ]
    ],
    'storage' => [
        'driver' => StorageDriver::Local
    ]
];
