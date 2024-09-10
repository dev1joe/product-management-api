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
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

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
        $data = $validator->validate($data);

        // create the category
        $category = $this->categoryService->create($data);

        // return $response->withHeader('Location', '/admin/category/all')->withStatus(302);

         return $response;
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
            $result = $this->categoryService->fetchPaginatedCategories($queryParams);

        } catch (ValidationException|MissingQueryParamsException $e) {
            $result = $this->categoryService->fetchAll();
        }
        // any other exception will pop into my face, haha.

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchById(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $arrayCategory = $this->categoryService->fetchByIdAsArray($id);

        $response->getBody()->write(json_encode($arrayCategory));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $category = $this->categoryService->fetchById($id);
        if(! $category) {
            return $response->withStatus(404);
        }

        $this->categoryService->delete($category);

        $successMessage = [
            'message' => 'Category deleted successfully',
            'deletedCategoryId' => $id,
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
        $data = $validator->validate($data);

        $id = (int) $args['id'];
        $category = $this->categoryService->fetchById($id);
        if(! $category) {
            return $response->withStatus(404);
        }

        // real update
        $this->categoryService->update($category, $data);

        $message = [
            'message' => 'category updated successfully!',
            'updatedCategoryId' => $id
        ];

        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}