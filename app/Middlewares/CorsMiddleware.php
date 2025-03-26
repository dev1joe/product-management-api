<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Config $config,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $allowedOrigin = $this->config->get('http.allowed_origin');
        $allowedMethods = 'GET, POST, OPTIONS, PATCH, DELETE';

        // TODO: browser's preflight OPTIONS request is causing errors with the DELETE request
        $response = $handler->handle($request);

        return $response->withHeader('Access-control-Allow-Origin', $allowedOrigin)
            ->withHeader('Access-control-Allow-Methods', $allowedMethods)
            ->withHeader('Access-control-Allow-Headers', '*');
    }
}