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
 * Makes sure that the client is <b>authorized</b> as a customer. <br>
 * Used with customer-specific routes (profile, shopping cart, and wishlist routes, etc...).
 */
class CustomerAuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(empty($_SESSION['userType']) || $_SESSION['userType'] !== UserType::Customer->value) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/login');
        }

        return $handler->handle($request);
    }
}