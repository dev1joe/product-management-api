<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

/**
 * - saves <strong> old data </strong> in session <br>
 * - saves <strong> validation errors </strong> in session <br>
 * - redirects user to the <strong> referer </strong> <br>
 */
class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $oldData = $request->getParsedBody();
            $sensitiveData = ['password', 'confirmPassword'];

            $_SESSION['old'] = array_diff_key($oldData, array_flip($sensitiveData));
            /**
             * array_diff_key() function compares keys
             * password and confirmPassword strings are values not keys in sensitiveData array
             * so we flip it to compare keys with each other
             */

            $_SESSION['errors'] = $e->errors;

            $referer = $request->getServerParams()['HTTP_REFERER'];
            $response = $this->responseFactory->createResponse()->withHeader('Location', $referer)->withStatus(302);

            // $response = $this->responseFactory->createResponse();
            // $response->getBody()->write(json_encode($e->errors));

            return $response;
        }
    }
}