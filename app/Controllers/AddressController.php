<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Address;
use App\Exceptions\ValidationException;
use App\Services\AddressService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AddressController
{
    public function __construct(
        private readonly AddressService $addressService,
    ){
    }

    public function fetchAll(Request $request, Response $response): Response {
        $data = $this->addressService->fetchPaginated();

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchById(Request $request, Response $response, $args) {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            throw new ValidationException(['id' => ["id not found in route arguments"]]);
        }

        $result = $this->addressService->fetchById($id);
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

}