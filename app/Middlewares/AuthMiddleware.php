<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Services\JwtService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly JwtService $jwtService,
    ){
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if(! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            $response = $this->responseFactory->createResponse(401);
            $response->getBody()->write(json_encode(['error' => 'Token missing']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = $this->jwtService->validateToken($token);

        if(! $decoded) {
            $response = $this->responseFactory->createResponse(401);
            $response->getBody()->write(json_encode(['error' => 'Invalid or expired token']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        if (empty($decoded->admin) || $decoded->admin != 'true') {
            $response = $this->responseFactory->createResponse(403);
            $response->getBody()->write(json_encode(['error' => 'Forbidden: Admins only']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        // Attach decoded token to request for later use
        $request = $request->withAttribute('token', $decoded);

        return $handler->handle($request);
    }
}