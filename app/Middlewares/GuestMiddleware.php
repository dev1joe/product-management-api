<?php
declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Makes sure that the customer is logged out
 */
class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(array_key_exists('customer', $_SESSION)) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }
}