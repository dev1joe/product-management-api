<?php
declare(strict_types=1);

namespace App\Controllers;

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
        $customers = $this->customerService->fetchAll();

        $response->getBody()->write(json_encode($customers));
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