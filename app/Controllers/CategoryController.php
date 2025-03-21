<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;
use App\QueryValidators\BaseQueryValidator;
use App\RequestValidators\CreateCategoryRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\CategoryService;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Throwable;

class CategoryController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly CategoryService $categoryService,
    ){
    }

    public function form(Request $request, Response $response): Response {
        return $this->twig->render($response, '/category/createCategory.twig');
    }

    public function create(Request $request, Response $response): Response {
        // get data
        $data = json_decode($request->getBody()->getContents(), true) ?? [];

        // get image
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($uploadedFiles['image'])) {
            $data['image'] = $uploadedFiles['image'];
        }

        // validate
        $validator = $this->requestValidatorFactory->make(CreateCategoryRequestValidator::class);

        try {
            $data = $validator->validate($data);
        } catch(ValidationException $e) {
            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        try {
            $this->categoryService->create($data);
        } catch(\Throwable $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }

        $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'category created successfully!']));
        return $response->withHeader('Content-Type','application/json')->withStatus(201);
    }

    public function fetchAllPaginated(Request $request, Response $response): Response {
        $queryParams = new QueryParams($request->getQueryParams());

        try {
            $queryValidator = new BaseQueryValidator(['updatedat', 'createdat', 'name', 'productcount', 'id']);
            $queryValidator->validate($queryParams);

            $result = $this->categoryService->fetchPaginated($queryParams);

            if(sizeof($result) == 0) return $response->withStatus(204);

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (ValidationException $e) {

            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        // any other exception will pop into my face, haha.
    }

    public function fetchNames(Request $request, Response $response): Response {
        $result = $this->categoryService->fetchIdsNames();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchById(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $category = $this->categoryService->fetchById($id);

        if(! $category) {
            $response->getBody()->write(json_encode(['error' => "Category Not Found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } else {
            $response->getBody()->write(json_encode($category));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->categoryService->delete($id);

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
            'message' => 'Category deleted successfully',
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

        if(isset($uploadedFiles['image'])) {
            $data['image'] = $uploadedFiles['image'];
        }

        $validator = $this->requestValidatorFactory->make(CreateCategoryRequestValidator::class);

        try {
            $data = $validator->validate($data);
        } catch(ValidationException $e) {
            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        try {
            $this->categoryService->update($id, $data);
            // TODO: handle EntityNotFoundException with 404 status code
        } catch (Throwable $e) {
            $response->getBody()->write(json_encode(['status' => 'fail', 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }

        $message = [
            'status' => 'success',
            'message' => 'category updated successfully!',
            'id' => $id
        ];

        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}