<?php
declare(strict_types=1);

namespace App\Controllers;

use http\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Entities\Product;
use Doctrine\ORM\EntityManager;

class ProductController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function fetchAll(Response $response): Response {
        $result = $this->entityManager
            ->getRepository(Product::class)->findAll();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response {
        //TODO: implement this function

        // get data from request

        // validate data using request validation

        // write in the DB

        throw new RuntimeException('function not implemented yet');
    }
}