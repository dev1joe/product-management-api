<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Category;
use App\RequestValidators\CreateProductRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\CategoryService;
use http\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Entities\Product;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;

class ProductController
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly Twig $twig,
        private readonly CategoryService $categoryService,
        private readonly RequestValidatorFactory $requestValidatorFactory,
    ){
    }

    public function form(Request $request, Response $response): Response {
        $categories = $this->categoryService->fetchCategoryNames();

        return $this->twig->render($response, '/forms/createProduct.twig', ['categories' => $categories]);
        //TODO: missing the photo input field
    }

    public function fetchAll(Request $request, Response $response): Response {
        $result = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')->select()->getQuery()->getArrayResult();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        $validator = $this->requestValidatorFactory->make(CreateProductRequestValidator::class);
        $data = $validator->validate($data);

        $product = new Product();
        $product->setName($data['name']);
        $product->setUnitPriceCents($data['price']);
        $product->setDescription($data['description']);
        $product->setPhoto($data['photo']);

        /** @var Category $category */
        $category = $data['category'];
        $category->incrementProductCount(1);
        $this->entityManager->persist($category);

        $product->setCategory($category);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $response->withHeader('Location', '/admin/products')->withStatus(302);
    }
}