<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Manufacturer;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ManufacturerController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function fetchNames(Request $request, Response $response): Response {
        //TODO: add service layer
        $result =  $this->entityManager->getRepository(Manufacturer::class)
            ->createQueryBuilder('m')
            ->select('m.id', 'm.name')
            ->getQuery()
            ->getArrayResult();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}