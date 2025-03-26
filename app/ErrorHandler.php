<?php
declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory
    ){
    }

    public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        // Determine the appropriate HTTP status code
        $statusCode = $exception instanceof HttpException
            ? $exception->getCode() ?: 500
            : 500;

        // Create JSON error response
        $errorPayload = [
            'status' => 'Fail',
            'message' => get_class($exception) . ": " . $exception->getMessage()
        ];

        $response->getBody()->write(json_encode($errorPayload));
        return $response->withStatus($statusCode)->withHeader('Content-Type', 'application/json');
    }
}