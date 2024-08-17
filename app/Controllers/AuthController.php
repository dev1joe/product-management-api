<?php
declare(strict_types=1);

namespace App\Controllers;

use App\RequestValidators\RegisterCustomerRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\CustomerService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly CustomerService $customerService,
    ){
    }

    public function loginForm(Request $request, Response $response): Response {
        return $this->twig->render($response, '/auth/login.twig');
    }

    public function logIn(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function registrationForm(Request $request, Response $response): Response {
        return $this->twig->render($response, '/auth/register.twig');
    }
    public function register(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        $validator = $this->requestValidatorFactory->make(RegisterCustomerRequestValidator::class);
        $data = $validator->validate($data);

        $this->customerService->create($data);

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}