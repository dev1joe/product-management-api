<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Address;
use App\Entities\Warehouse;
use App\Exceptions\MethodNotImplementedException;
use App\RequestValidators\CreateWarehouseRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\AddressService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class WarehouseController
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly Twig $twig,
        private readonly AddressService $addressService
    ){
    }

    public function form(Request $request, Response $response): Response {

        $addresses = $this->addressService->fetchAllIdsDetails();

        return $this->twig->render($response, '/forms/createWarehouse.twig', ['addresses' => $addresses]);
    }

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();

        $validator = $this->requestValidatorFactory->make(CreateWarehouseRequestValidator::class);
        $data = $validator->validate($data);

        $warehouse = new Warehouse();
        $warehouse->setName($data['name']);

        if(array_key_exists('address_id', $data)) {
            $address = $data['address_id'];
        } else {
            $address = new Address();

            $address->setCountry($data['country']);
            $address->setGovernorate($data['governorate']);
            $address->setDistrict($data['district']);
            $address->setStreet($data['street']);
            $address->setBuilding($data['building']);

            $this->entityManager->persist($address);

        }

        $warehouse->setAddress($address);

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        // return $response->withHeader('Location', '/admin/warehouse/all')->withStatus(302);
        $message = ['massage' => 'warehouse created successfully!'];
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function fetchAll(Request $request, Response $response): Response {
        $result = $this->entityManager->getRepository(Warehouse::class)
            ->createQueryBuilder('w')->select('w', 'a')
            ->leftJoin('w.address', 'a')
            ->getQuery()->getArrayResult();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchById(Request $request, Response $response): Response {
        throw new MethodNotImplementedException();
    }
}