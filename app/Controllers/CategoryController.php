<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DataObjects\CategoryQueryParams;
use App\Exceptions\MissingQueryParamsException;
use App\Exceptions\ValidationException;
use App\QueryValidators\CategoryQueryValidator;
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
        $data = $request->getParsedBody();

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
        return $response->withHeader('Content-Type','application/json')->withStatus(200);
    }

    public function fetchAll(Request $request, Response $response) {
        $result = $this->categoryService->fetchAll();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchAllPaginated(Request $request, Response $response): Response {
        $queryParams = new CategoryQueryParams($request->getQueryParams());

        try {
            (new CategoryQueryValidator())->validate($queryParams);
            $result = $this->categoryService->fetchPaginated($queryParams);
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
        $id = (int) $args['id'];
        $arrayCategory = $this->categoryService->fetchById($id);

        $response->getBody()->write(json_encode($arrayCategory));
        return $response->withHeader('Content-Type', 'application/json');
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
            'deletedId' => $id,
        ];

        $response->getBody()->write(json_encode($successMessage));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function update(Request $request, Response $response, array $args): Response {
        // get and validate data FIRST
        $data = $request->getParsedBody();
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

        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->categoryService->update($id, $data);
        } catch (Throwable $e) {
            $response->getBody()->write(json_encode(['status' => 'fail', 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }

        $message = [
            'message' => 'category updated successfully!',
            'updatedCategoryId' => $id
        ];

        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}