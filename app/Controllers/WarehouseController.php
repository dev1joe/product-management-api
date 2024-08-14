<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Address;
use App\Entities\Warehouse;
use App\Exceptions\MethodNotImplementedException;
use App\RequestValidators\CreateWarehouseRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\RequestValidators\WarehouseUpdateValidator;
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

        $this->addressService->create($data);

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
        // $response->getBody()->write(json_encode($data));
        // return $response->withHeader('Content-Type', 'application/json');

        $validator = $this->requestValidatorFactory->make(CreateWarehouseRequestValidator::class);
        $data = $validator->validate($data);

        // what if the data is not changed ???
        $this->requestValidatorFactory->make(WarehouseUpdateValidator::class)->validate($data);

        // given that the warehouse data is changed at this point, we can proceed to create a new warehouse object
        $this->addressService->create($data); //TODO: must be moved to the warehouse service !!!!!!!!

        /** @var Warehouse $oldWarehouse */
        $oldWarehouse = $this->entityManager->find(Warehouse::class, (int) $data['id']);
        $this->entityManager->remove($oldWarehouse);
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