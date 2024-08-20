<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Enums\UserType;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * only lets the client in if they are logged in and an admin
 */
class AdminAuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(empty($_SESSION['userType'])) {
            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', '/admin/login');
        }

        if(strtolower($_SESSION['userType']) !== strtolower(UserType::Admin->value)) {
            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }
}