<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Address;
use App\Services\AddressService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AddressController
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly AddressService $addressService,
    ){
    }

    public function fetchAll(Request $request, Response $response): Response {
        $addresses = $this->addressService->fetchAllAddresses();

        $response->getBody()->write(json_encode($addresses));
        return $response;
    }

}