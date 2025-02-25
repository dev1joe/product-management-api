<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Manufacturer;
use App\Exceptions\ValidationException;
use App\Services\ManufacturerService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ManufacturerController
{
    public function __construct(
        private readonly ManufacturerService $manufacturerService,
    ){
    }

    public function fetchAllPaginated(Request $request, Response $response) {
        $result = $this->manufacturerService->fetchPaginated();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');

    }

    public function fetchById(Request $request, Response $response, $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            throw new ValidationException(['id' => ["id not found in route arguments"]]);
        }

        $result = $this->manufacturerService->fetchById($id);
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchNames(Request $request, Response $response): Response {
        $result =  $this->manufacturerService->fetchIdsNames();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}