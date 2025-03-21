<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DataObjects\QueryParams;
use App\Entities\Manufacturer;
use App\Exceptions\MethodNotImplementedException;
use App\Exceptions\ValidationException;
use App\QueryValidators\BaseQueryValidator;
use App\RequestValidators\CreateManufacturerRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\ManufacturerService;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

class ManufacturerController
{
    public function __construct(
        private readonly ManufacturerService $manufacturerService,
        private readonly RequestValidatorFactory $requestValidatorFactory,
    ){
    }

    public function fetchAllPaginated(Request $request, Response $response) {
        $queryParams = new QueryParams($request->getQueryParams());

        try {
            $queryValidator = new BaseQueryValidator(['updatedat', 'createdat', 'name', 'id', 'productcount']);
            $queryValidator->validate($queryParams);

            $result = $this->manufacturerService->fetchPaginated($queryParams);

            if(sizeof($result) == 0) return $response->withStatus(204);

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch(ValidationException $e) {

            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function fetchById(Request $request, Response $response, $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $manufacturer = $this->manufacturerService->fetchById($id);

        if(! $manufacturer) {
            $response->getBody()->write(json_encode(['error' => "Manufacturer Not Found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } else {
            $response->getBody()->write(json_encode($manufacturer));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function fetchNames(Request $request, Response $response): Response {
        $result =  $this->manufacturerService->fetchIdsNames();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->manufacturerService->delete($id);

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
            'message' => 'Manufacturer deleted successfully',
            'id' => $id,
        ];

        $response->getBody()->write(json_encode($successMessage));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function update(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $data = json_decode($request->getBody()->getContents(), true) ?? [];
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($uploadedFiles['logo'])) {
            $data['logo'] = $uploadedFiles['logo'];
        }

        $validator = $this->requestValidatorFactory->make(CreateManufacturerRequestValidator::class);

        try {
            $data = $validator->validate($data);
        } catch(ValidationException $e) {
            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        try {
            $this->manufacturerService->update($id, $data);
        } catch (Throwable $e) {
            $response->getBody()->write(json_encode(['status' => 'fail', 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }

        $message = [
            'status' => 'success',
            'message' => 'manufacturer updated successfully!',
            'id' => $id
        ];

        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function create(Request $request, Response $response): Response {
        // get data
        $data = json_decode($request->getBody()->getContents(), true) ?? [];

        // get image
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($uploadedFiles['logo'])) {
            $data['logo'] = $uploadedFiles['logo'];
        }

        // validate
        $validator = $this->requestValidatorFactory->make(CreateManufacturerRequestValidator::class);

        try {
            $data = $validator->validate($data);
        } catch(ValidationException $e) {
            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        try {
            $this->manufacturerService->create($data);
        } catch(\Throwable $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }

        $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'manufacturer created successfully!']));
        return $response->withHeader('Content-Type','application/json')->withStatus(201);
    }
}