<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            //TODO: continue this middleware
            // redirect back to the referer
            // show errors under input fields

            // $referer = $request->getServerParams()['HTTP_REFERER'];
            // $response = $this->responseFactory->createResponse()->withHeader('Location', $referer)->withStatus(302);

            $response = $this->responseFactory->createResponse();
            $response->getBody()->write(json_encode($e->errors));

            return $response;
        }
    }
}