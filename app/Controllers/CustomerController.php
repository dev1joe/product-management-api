<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\CustomerService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CustomerController
{
    public function __construct(
        private readonly CustomerService $customerService,
    ){
    }

    public function fetchAll(Request $request, Response $response): Response {
        $customers = $this->customerService->fetchAll();

        $response->getBody()->write(json_encode($customers));
        return $response->withHeader('Content-Type', 'application/json');
    }
}