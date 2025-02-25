<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;

class AjaxHandleExceptionsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory
    ){
    }

    private function createResponse(int $responseCode, string $message): ResponseInterface {
        $response = $this->responseFactory->createResponse($responseCode)->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($message);
        return $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            var_dump("Middleware executed for: " . $request->getUri()->getPath());
            error_log('processing request from API exception Handler');
            echo 'processing request from API exception Handler';
            return $handler->handle($request);

        } catch (ValidationException $e) {

            // bad request
            return $this->createResponse(400, json_encode($e->errors));

        } catch (HttpNotFoundException $e) {
            // Not found
            error_log($e->getMessage());
            return $this->createResponse(
                404,
                json_encode(['error' => 'Resource Does Not Exist!'])
            );

        } catch (\RuntimeException $e) {
            // handling other exception assuming that other exceptions are server errors
            error_log($e->getMessage());

            $response = $this->responseFactory->createResponse(500)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($e->getMessage()));
            return $response;
        }
    }
}