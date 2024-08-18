<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Category;
use App\Exceptions\MethodNotImplementedException;
use App\RequestValidators\CreateProductRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\RequestValidators\UploadProductPhotoRequestValidator;
use App\Services\CategoryService;
use App\Services\ProductService;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Entities\Product;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Views\Twig;

class ProductController
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly Twig $twig,
        private readonly CategoryService $categoryService,
        private readonly RequestValidatorFactory $requestValidatorFactory,
        private readonly Filesystem $filesystem,
        private readonly ProductService $productService,
    ){
    }
    // TODO: refactor to product service
    public function form(Request $request, Response $response): Response {
        $categories = $this->categoryService->fetchCategoryNames();

        return $this->twig->render($response, '/product/createProduct.twig', ['categories' => $categories]);
        //TODO: missing the photo input field
    }

    public function fetchAll(Request $request, Response $response): Response {
        $result = $this->productService->fetchAll();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        // $response->getBody()->write(json_encode($data));
        // return $response->withHeader('Content-Type', 'application/json');

        $validator = $this->requestValidatorFactory->make(CreateProductRequestValidator::class);
        $data = $validator->validate($data);

        $product = new Product();
        $product->setName($data['name']);
        $product->setUnitPriceCents($data['price']);
        $product->setDescription($data['description']);

        // photo handling
        /** @var UploadedFileInterface $file */
        $file = $this->requestValidatorFactory->make(
            UploadProductPhotoRequestValidator::class
        )->validate($request->getUploadedFiles())['photo'];

        $fileName = $file->getClientFilename();

        $this->filesystem->write($fileName, $file->getStream()->getContents());

        $relativeLocation = '/storage/'.$fileName;
        $product->setPhoto($relativeLocation);

        // category handling
        /** @var Category $category */
        $category = $data['category'];
        $category->incrementProductCount(1);
        $this->entityManager->persist($category);

        $product->setCategory($category);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // return $response->withHeader('Location', '/admin/product/all')->withStatus(302);

        // TODO: redirect to "all products page" when it's present
        $message = ['massage' => 'product created successfully!'];
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }

    public function update(Request $request, Response $response, array $args): Response {
        $data = $this->requestValidatorFactory->make(CreateProductRequestValidator::class)
            ->validate($request->getParsedBody());

        $id = (int) $args['id'];
        /** @var Product $product */
        $product = $this->entityManager->find(Product::class, $id);

        if(! $product) {
            return $response->withStatus(404);
        }

        $product->setName($data['name']);
        $product->setPhoto($data['photo']); //TODO: photo handling
        $product->setDescription($data['description']);
        $product->setUnitPriceCents($data['price']); // price already handled by the request validator

        $currentCategory = $product->getCategory();

        /** @var Category $newCategory */
        $newCategory = $data['category'];

        if($currentCategory->getId() !== $newCategory->getId()) {
            $currentCategory->decrementProductCount(1);

            $product->setCategory($newCategory);
            $newCategory->incrementProductCount(1);

            $this->entityManager->persist($currentCategory);
            $this->entityManager->persist($newCategory);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $message = [
            'message' => 'Product updated successfully!',
            'updatedProductId' => $id
        ];

        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];

        /** @var Product $product */
        $product = $this->entityManager->find(Product::class, $id);

        if(! $product) {
            return $response->withStatus(404);
        }

        ($product->getCategory())->decrementProductCount(1);

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $successMessage = [
            'message' => 'Product deleted successfully',
            'deletedProductId' => $id,
        ];

        $response->getBody()->write(json_encode($successMessage));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}