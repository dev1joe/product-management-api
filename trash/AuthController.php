<?php
declare(strict_types=1);

// namespace App\Controllers;

use App\Contracts\AuthServiceInterface;
use App\Exceptions\ValidationException;
use App\RequestValidators\RegisterCustomerRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Valitron\Validator;

class AuthController
{
    public function __construct(
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly AuthServiceInterface $authService,
    ){
    }

    public function loginForm(Request $request, Response $response): Response {
        return $this->twig->render(
            $response,
            '/auth/login.twig',
            ['register' => true, 'userType' => UserType::Customer->value]
        );
    }

    public function adminLoginForm(Request $request, Response $response): Response {
        return $this->twig->render(
            $response,
            '/auth/login.twig',
            ['register' => false, 'userType' => UserType::Admin->value]
        );
    }

    public function logIn(Request $request, Response $response): Response {
        // get data
        $data = $request->getParsedBody();

        // validate data
        $v = new Validator($data);
        $v->rule('required', ['email', 'password', 'userType']);
        $v->rule('email', 'email');
        $v->rule('alpha', 'userType');

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        $userTypeKey = $data['userType'];

        $userTypeKey = ($userTypeKey === 'customer')? UserType::Customer : UserType::Admin;

        if(! $this->authService->attemptLogIn($data, $userTypeKey)) {
            throw new ValidationException(['password' => ['invalid email or password']]);
        }

        // redirect
        $location = ($userTypeKey === UserType::Customer)? '/' : '/admin/dashboard';
        return $response->withHeader('Location', $location)->withStatus(302);
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
        $this->authService->logOut(); // TODO: how can this function have access to the userTypeKey ??

        // redirect to home page, because home page doesn't need authentication
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}