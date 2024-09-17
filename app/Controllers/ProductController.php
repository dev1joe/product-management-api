<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DataObjects\ProductQueryParams;
use App\Entities\Category;
use App\Exceptions\MethodNotImplementedException;
use App\Exceptions\MissingQueryParamsException;
use App\Exceptions\ValidationException;
use App\QueryValidators\ProductQueryValidator;
use App\RequestValidators\CreateProductRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\RequestValidators\UploadProductPhotoRequestValidator;
use App\Services\CategoryService;
use App\Services\ProductService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Entities\Product;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Views\Twig;

class ProductController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly CategoryService $categoryService,
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly ProductService $productService,
    ){
    }

    /**
     * @throws OptimisticLockException
     * @throws FilesystemException
     * @throws ORMException
     */
    public function create(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        if(array_key_exists('photo', $request->getUploadedFiles())) {
            $data['photo'] = $request->getUploadedFiles()['photo'];
        }

        // $response->getBody()->write(json_encode($data));
        // return $response->withHeader('Content-Type', 'application/json');

        $validator = $this->requestValidatorFactory->make(CreateProductRequestValidator::class);
        $data = $validator->validate($data);

        $this->productService->create($data);

        $response->getBody()->write(json_encode(['message' => 'product created successfully!']));
        return $response->withHeader('Content-Type','application/json')->withStatus(200);
    }
    public function form(Request $request, Response $response): Response {
        $categories = $this->categoryService->fetchCategoryNames();

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

        $product = $this->productService->fetchByIdAsArray($id);
        $response->getBody()->write(json_encode($product));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchAllPaginated(Request $request, Response $response): Response {
        $queryParams = new ProductQueryParams($request->getQueryParams());

        try {
            (new ProductQueryValidator())->validate($queryParams);
            $result = $this->productService->fetchPaginatedProducts($queryParams);
        } catch(ValidationException|MissingQueryParamsException $e) {
            $result = $this->productService->fetchAll();
        }
        //TODO: do not fetch all
        //TODO: 404 response for missing query parameters exception
        //TODO: xxx response for validation exception
        //TODO: xxx response for ORM\QueryException
        //TODO: exception handling
        //TODO: same todos for category

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws FilesystemException
     */
    public function update(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($uploadedFiles['photo'])) {
            $data['photo'] = $uploadedFiles['photo'];
        }

        $validator = $this->requestValidatorFactory->make(CreateProductRequestValidator::class);
        $data = $validator->validate($data);

        $id = (int) $args['id'];
        $product = $this->productService->fetchById($id);
        if(! $product) {
            return $response->withStatus(404);
        }

        [$isChanged, $message] = $this->productService->update($product, $data);
        if($isChanged) {
            $status = 200;
            // $message = 'product updated successfully';
        } else {
            $status = 400;
            // $message = 'product information not changed';
        }

        $response->getBody()->write(json_encode(['message' => $message]));
        return $response->withHeader('Content-Type','application/json')->withStatus($status);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];

        $product = $this->productService->fetchById($id);
        if(! $product) {
            return $response->withStatus(404);
        }

//        $category = $product->getCategory();
//        $category->decrementProductCount(1);
//        $this->entityManager->persist($category);
        $this->productService->delete($product);

        $successMessage = [
            'message' => 'Product deleted successfully',
            'deletedProductId' => $id,
        ];

        $response->getBody()->write(json_encode($successMessage));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}