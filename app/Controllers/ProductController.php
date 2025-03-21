<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DataObjects\ProductQueryParams;
use App\Exceptions\ValidationException;
use App\QueryValidators\BaseQueryValidator;
use App\RequestValidators\CreateProductRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\RequestValidators\UpdateProductRequestValidator;
use App\Services\CategoryService;
use App\Services\ProductService;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Throwable;

class ProductController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly CategoryService $categoryService,
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly ProductService $productService,
    ){
    }

    public function create(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true) ?? [];

        if(array_key_exists('photo', $request->getUploadedFiles())) {
            $data['photo'] = $request->getUploadedFiles()['photo'];
        }

        $validator = $this->requestValidatorFactory->make(CreateProductRequestValidator::class);

        try {
            $data = $validator->validate($data);
        } catch(ValidationException $e) {
            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        try {
            $this->productService->create($data);
        } catch(\Throwable $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }

        $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'product created successfully!']));
        return $response->withHeader('Content-Type','application/json')->withStatus(201);
    }

    public function form(Request $request, Response $response): Response {
        $categories = $this->categoryService->fetchIdsNames();

        return $this->twig->render($response, '/product/newCreateProduct.twig', ['categories' => $categories]);
    }

    public function productPage(Request $request, Response $response): Response {
        return $this->twig->render($response, '/product.twig');
    }

    public function fetchById(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            throw new ValidationException(['id' => ["id not found in route arguments"]]);
        }

        $product = $this->productService->fetchById($id);

        if(! $product) {
            $response->getBody()->write(json_encode(['error' => "Product Not Found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);

        } else {
            $response->getBody()->write(json_encode($product));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function fetchAllPaginated(Request $request, Response $response): Response {
        $queryParams = new ProductQueryParams($request->getQueryParams());

        try {
            $queryValidator = new BaseQueryValidator(['createdat', 'updatedat', 'unitpriceincents', 'avgrating', 'id', 'name']);
            $queryValidator->validate($queryParams);

            $result = $this->productService->fetchPaginated($queryParams);

            if(sizeof($result) == 0) return $response->withStatus(204);

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');

        } catch(ValidationException $e) {

            $response->getBody()->write(json_encode(['errors' => $e->errors]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        //TODO: xxx response code for ORM\QueryException
        //TODO: same todos for category
    }

    public function update(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $data = json_decode($request->getBody()->getContents(), true) ?? [];
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($uploadedFiles['photo'])) {
            $data['photo'] = $uploadedFiles['photo'];
        }

        $validator = $this->requestValidatorFactory->make(UpdateProductRequestValidator::class);

        try {
            $data = $validator->validate($data);
        } catch(ValidationException $e) {
            $response->getBody()->write(json_encode(['errors' => $e->errors, 'data' => $data]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        try {
            $this->productService->update($id, $data);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Product Updated Successfully',
                'id' => $id,
            ]));
            return $response->withHeader('Content-Type','application/json')->withStatus(200);

        } catch(EntityNotFoundException $e) {
            $message = [
                'status' => 'fail',
                'message' => $e->getMessage()
            ];

            $response->getBody()->write(json_encode($message));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);

        } catch(Throwable $e) {
            $response->getBody()->write(json_encode(['status' => 'fail', 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type','application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (array_key_exists('id', $args))? (int) $args['id'] : null;

        if(! $id) {
            $response->getBody()->write(json_encode(['id' => "id not found in route arguments"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

//        $category = $product->getCategory();
//        $category->decrementProductCount(1);
//        $this->entityManager->persist($category);
        try {
            $this->productService->delete($id);

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
            'message' => 'Product deleted successfully',
            'id' => $id,
        ];

        $response->getBody()->write(json_encode($successMessage));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}