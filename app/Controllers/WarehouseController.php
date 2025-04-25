<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;
use App\QueryValidators\BaseQueryValidator;
use App\RequestValidators\CreateWarehouseRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\RequestValidators\UpdateWarehouseRequestValidator;
use App\Services\WarehouseService;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

class WarehouseController
{
    public function __construct(
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly WarehouseService $warehouseService,
    ){
    }

    public function create(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true) ?? [];

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
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function fetchAllPaginated(Request $request, Response $response): Response {
        $queryParams = new QueryParams($request->getQueryParams());

        try {
            $queryValidator = new BaseQueryValidator(['updatedat', 'createdat', 'name', 'id']);
            $queryValidator->validate($queryParams);

            $result = $this->warehouseService->fetchPaginated($queryParams);

            if(sizeof($result) == 0) return $response->withStatus(204);

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

    public function update(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }


        $data = json_decode($request->getBody()->getContents(), true) ?? [];
        $validator = $this->requestValidatorFactory->make(UpdateWarehouseRequestValidator::class);

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
}