<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Contracts\AuthServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Gets the customer id persisted in the session super-global <br>
 * Gets the customer instance <br>
 * Attaches it as an attribute of the request <br>
 */
class AuthenticateMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute('customer', $this->authService->getAuthenticatedUser()));
    }
}