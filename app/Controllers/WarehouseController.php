<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DataObjects\QueryParams;
use App\Entities\Warehouse;
use App\Exceptions\ValidationException;
use App\QueryValidators\BaseQueryValidator;
use App\RequestValidators\CreateWarehouseRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\AddressService;
use App\Services\WarehouseService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Throwable;

class WarehouseController
{
    // TODO: refactor to warehouse service
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

        return $this->twig->render($response, '/warehouse/createWarehouse.twig', ['addresses' => $addresses]);
    }

    public function create(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        $validator = $this->requestValidatorFactory->make(CreateWarehouseRequestValidator::class);

        try {
            $data = $validator->validate($data);
        } catch(ValidationException $e) {
            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        try {
            $this->warehouseService->create($data);
        } catch(Throwable $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }

        $message = ['status' => 'success', 'massage' => 'warehouse created successfully!'];
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function fetchAllPaginated(Request $request, Response $response): Response {
        $queryParams = new QueryParams($request->getQueryParams());

        try {
            $queryValidator = new BaseQueryValidator(['updatedat', 'createdat', 'name', 'id']);
            $queryValidator->validate($queryParams);

            $result = $this->warehouseService->fetchPaginated($queryParams);

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (ValidationException $e) {

            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function fetchById(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => ["id not found in route arguments"]]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $warehouse = $this->warehouseService->fetchById($id);

        if(! $warehouse) {
            $response->getBody()->write(json_encode(['error' => "Warehouse Not Found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } else {
            $response->getBody()->write(json_encode($warehouse));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function updateForm(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $warehouse = $this->entityManager->getRepository(Warehouse::class)->createQueryBuilder('w')
            ->select('w', 'a')->leftJoin('w.address', 'a')->where('w.id = :id')->setParameter('id', $id)
            ->getQuery()->getArrayResult()[0];

        $addresses = $this->addressService->fetchAllIdsDetails();
        return $this->twig->render(
            $response,
            '/warehouse/updateWarehouse.twig',
            [
                'warehouse' => $warehouse,
                'addresses' => $addresses,
            ]
        );
    }

    public function update(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // TODO: validate that data is not null
        $data = $request->getParsedBody();
        $validator = $this->requestValidatorFactory->make(CreateWarehouseRequestValidator::class);

        try {
            $data = $validator->validate($data);
        } catch (ValidationException $e) {
            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        try {
            $this->warehouseService->update($id, $data);
        } catch (Throwable $e) {
            $response->getBody()->write(json_encode(['status' => 'fail', 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }

        $message = [
            'status' => 'success',
            'message' => 'warehouse updated successfully!',
            'id' => $id
        ];

        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // TODO: delete address when no more warehouses associated with it ??
    public function delete(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->warehouseService->delete($id);

        } catch(EntityNotFoundException $e) {
            $message = [
                'status' => 'fail',
                'message' => $e->getMessage()
            ];

            $response->getBody()->write(json_encode($message));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } catch(Throwable $e) {
            $message = [
                'status' => 'fail',
                'message' => $e->getMessage()
            ];

            $response->getBody()->write(json_encode($message));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $successMessage = [
            'status' => 'success',
            'message' => 'Warehouse deleted successfully',
            'id' => $id,
        ];

        $response->getBody()->write(json_encode($successMessage));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

//    public function delete(Request $request, Response $response, array $args): Response {
//        $id = (int) $args['id'];
//        $isAddressDeleted = false;
//
//        /** @var Warehouse $warehouse */
//        $warehouse = $this->entityManager->find(Warehouse::class, $id);
//
//        $address = $warehouse->getAddress();
//
//        // if warehouse address is NOT associated with another object, DELETE IT
//        $qb = $this->entityManager->getRepository(Warehouse::class)->createQueryBuilder('w');
//        $addressAssociatedWarehouses = $qb->select('w.id')->leftJoin('w.address', 'a')->where(
//            'a.id = :addressId'
//        )->setParameter('addressId', $address->getId())->getQuery()->getArrayResult();
//
//        if(sizeof($addressAssociatedWarehouses) == 1) {
//            $this->entityManager->remove($address);
//            $isAddressDeleted = true;
//        }
//
//        $this->entityManager->remove($warehouse);
//        $this->entityManager->flush();
//
//        $successMessage = [
//            'message' => $warehouse->getName().' deleted successfully !',
//            'deletedAddressId' => $id,
//            'associatedAddressDeleted' => $isAddressDeleted,
//            'associationsQueryResult' => json_encode($addressAssociatedWarehouses)
//        ];
//
//        $response->getBody()->write(json_encode($successMessage));
//        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
//    }
}