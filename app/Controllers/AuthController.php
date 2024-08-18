<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\AuthServiceInterface;
use App\Exceptions\MethodNotImplementedException;
use App\Exceptions\ValidationException;
use App\RequestValidators\RegisterCustomerRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\CustomerService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Valitron\Validator;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly CustomerService $customerService,
        private readonly AuthServiceInterface $authService,
    ){
    }

    public function loginForm(Request $request, Response $response): Response {
        return $this->twig->render($response, '/auth/login.twig');
    }

    public function logIn(Request $request, Response $response): Response {
        // get data
        $data = $request->getParsedBody();

        // validate data
        $v = new Validator($data);
        $v->rule('required', ['email', 'password']);
        $v->rule('email', 'email');

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        if(! $this->authService->attemptLogIn($data)) {
            throw new ValidationException(['password' => ['invalid email or password']]);
        }

        // redirect
        return $response->withHeader('Location', '/')->withStatus(302);
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

    public function logOut(Request $request, Response $response): Response {
        $this->authService->logOut();

        // redirect to home page, because home page doesn't need authentication
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}