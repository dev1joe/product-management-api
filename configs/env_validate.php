<?php
declare(strict_types=1);

use Dotenv\Dotenv;

return function(Dotenv $dotenv) {
    // required fields
    $dotenv->required('DB_DRIVER')->allowedValues(['pdo_mysql', 'mysqli', 'pdo_pgsql', 'pgsql', 'pdo_sqlsrv', 'sqlsrv']);
    $dotenv->required('DB_HOST')->notEmpty();
    $dotenv->required('DB_NAME')->notEmpty();
    $dotenv->required('DB_USER')->notEmpty();
    $dotenv->required('DB_PASS')->notEmpty();
    $dotenv->required('JWT_SECRET')->notEmpty();

    // optional fields
    $dotenv->ifPresent('APP_ENV')->allowedValues(['development', 'production']);
    $dotenv->ifPresent('JWT_ALG')->allowedValues(['ES384', 'ES256', 'ES256K', 'HS256', 'HS384', 'HS512', 'RS256', 'RS384', 'RS512']);
    $dotenv->ifPresent('HTTP_ALLOWED_ORIGIN')->notEmpty();
    $dotenv->ifPresent('APP_NAME')->notEmpty();
};