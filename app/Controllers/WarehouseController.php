<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Warehouse;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class WarehouseController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function fetchAll(Request $request, Response $response) {
        $result = $this->entityManager
            ->getRepository(Warehouse::class)->findAll();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response) {

    }
}