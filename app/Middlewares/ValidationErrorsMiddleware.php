<?php
declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class ValidationErrorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Twig $twig,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(! empty($_SESSION['errors'])) {
            $this->twig->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
            $this->twig->getEnvironment()->addGlobal('old', $_SESSION['old']);

            unset($_SESSION['errors']);
            unset($_SESSION['old']);
        }

        return $handler->handle($request);
    }
}