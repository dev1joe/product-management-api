<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Services\CustomerService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class CustomerController
{
    public function __construct(
        private readonly CustomerService $customerService,
        private readonly Twig $twig,
    ){
    }

    public function fetchAll(Request $request, Response $response): Response {
        $email = $request->getQueryParams()['email'] ?? null;

        if($email) {
            $data = $this->customerService->fetchByEmail($email);
        } else {
            $data = $this->customerService->fetchPaginated();
        }

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchById(Request $request, Response $response, $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            throw new ValidationException(['id' => ["id not found in route arguments"]]);
        }

        $result = $this->customerService->fetchById($id);
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchByEmail(Request $request, Response $response, array $args): Response {
        $email = (array_key_exists('email', $args))? $args['email'] : null;

        if(! $email) {
            throw new ValidationException(['email' => 'email not found in route arguments']);
        }

        $result = $this->customerService->fetchByEmail($email);
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function profile(Request $request, Response $response): Response {
        return $this->twig->render($response, '/customer/profile.twig');
    }

    public function wishlist(Request $request, Response $response): Response {
        $response->getBody()->write("<h1>Hi this is the wishlist, have a good day &#x1F60A;</h1>");
        return $response->withHeader('Content-Type', 'text/html');
    }
}