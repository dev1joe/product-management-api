<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Address;
use App\Entities\Warehouse;
use App\Exceptions\MethodNotImplementedException;
use App\Exceptions\ValidationException;
use App\RequestValidators\CreateWarehouseExistingAddressRequestValidator;
use App\RequestValidators\CreateWarehouseNewAddressRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\AddressService;
use App\Services\WarehouseService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class WarehouseController
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly Twig $twig,
        private readonly AddressService $addressService,
        private readonly WarehouseService $warehouseService,
    ){
    }

    public function form(Request $request, Response $response): Response {

        $addresses = $this->addressService->fetchAllIdsDetails();

        return $this->twig->render($response, '/forms/createWarehouse.twig', ['addresses' => $addresses]);
    }

    public function create(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        if(array_key_exists('address_type', $data)) {
            $addressType = $data['address_type'];

            if($addressType == 'existing') {

                $data = $this->requestValidatorFactory->make(
                    CreateWarehouseExistingAddressRequestValidator::class
                )->validate($data);

                $this->warehouseService->createWithAddress($data['name'], $data['address']);

            } else if($addressType == 'new') {

                $data = $this->requestValidatorFactory->make(
                    CreateWarehouseNewAddressRequestValidator::class
                )->validate($data);

                $this->warehouseService->create($data);

            } else {
                throw new ValidationException(['address_type' => 'address type must only be new or exiting, address type is ' . $addressType . '.']);
            }

        } else {
            throw new ValidationException(['address_type' => 'address type is required']);
        }

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
        throw new MethodNotImplementedException(); //TODO: implement this function
    }

    public function updateForm(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $warehouse = $this->entityManager->getRepository(Warehouse::class)->createQueryBuilder('w')
            ->select('w', 'a')->leftJoin('w.address', 'a')->where('w.id = :id')->setParameter('id', $id)
            ->getQuery()->getArrayResult()[0];

        $addresses = $this->addressService->fetchAllIdsDetails();
        return $this->twig->render(
            $response,
            '/forms/createWarehouse.twig',
            [
                'warehouse' => $warehouse,
                'addresses' => $addresses,
            ]
        );
    }

    public function update(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $id = null;

        if(array_key_exists('id', $data)) {
            $id = (int) $data['id'];
        } else {
            $id = (int) $args['id'];
        }

        $warehouse = $this->entityManager->find(Warehouse::class, $id);

        if(! $warehouse) {
            throw new ValidationException(['name' => 'Warehouse not found']);
        }

        if(array_key_exists('address_type', $data)) {
            $addressType = $data['address_type'];

            if($addressType == 'existing') {

                $data = $this->requestValidatorFactory->make(
                    CreateWarehouseExistingAddressRequestValidator::class
                )->validate($data);

                // make sure data changed
                // either different address
                // or different name

                /** @var Address $newAddress */
                $newAddress = $data['address'];

                if(
                    $warehouse->getAddress()->getId() === $newAddress->getId() &&
                    $warehouse->getName() === $data['name']
                ) {
                    throw new ValidationException([
                        'name' => 'either name or address has to be changed',
                        'address' => 'either name or address has to be changed',
                    ]);
                }

                $warehouse->setName($data['name']);
                $warehouse->setAddress($newAddress);

            } else if($addressType == 'new') {

                $data = $this->requestValidatorFactory->make(
                    CreateWarehouseNewAddressRequestValidator::class
                )->validate($data);

                $warehouse->setName($data['name']);

                // create and assign new address
                $address = $this->addressService->create($data); // the address is flushed by the create function
                $warehouse->setAddress($address);

            } else {
                throw new ValidationException(['address_type' => 'address type must only be new or exiting, address type is ' . $addressType . '.']);
            }

        } else {
            throw new ValidationException(['address_type' => 'address type is required']);
        }

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        $message = "update successful !";
        $response->getBody()->write(json_encode(['message' => $message]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $isAddressDeleted = false;

        /** @var Warehouse $warehouse */
        $warehouse = $this->entityManager->find(Warehouse::class, $id);

        $address = $warehouse->getAddress();

        // if warehouse address is NOT associated with another object, DELETE IT
        $qb = $this->entityManager->getRepository(Warehouse::class)->createQueryBuilder('w');
        $addressAssociatedWarehouses = $qb->select('w.id')->leftJoin('w.address', 'a')->where(
            'a.id = :addressId'
        )->setParameter('addressId', $address->getId())->getQuery()->getArrayResult();

        if(sizeof($addressAssociatedWarehouses) == 1) {
            $this->entityManager->remove($address);
            $isAddressDeleted = true;
        }

        $this->entityManager->remove($warehouse);
        $this->entityManager->flush();

        $successMessage = [
            'message' => $warehouse->getName().' deleted successfully !',
            'deletedAddressId' => $id,
            'associatedAddressDeleted' => $isAddressDeleted,
            'associationsQueryResult' => json_encode($addressAssociatedWarehouses)
        ];

        $response->getBody()->write(json_encode($successMessage));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}