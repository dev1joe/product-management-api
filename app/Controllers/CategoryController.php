<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Category;
use App\Exceptions\MethodNotImplementedException;
use App\RequestValidators\CreateCategoryRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use App\Services\CategoryService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class CategoryController
{
    // TODO: refactor to category service
    public function __construct(
        private readonly EntityManager $entityManager,
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
        $queryParams = $request->getQueryParams();

        // I need two parameters, orderBy and orderDir, can't use only one
        if(
            sizeof($queryParams) === 2 &&
            array_key_exists('orderDir', $queryParams) &&
            in_array(strtolower($queryParams['orderDir']), ['asc', 'desc']) &&
            array_key_exists('orderBy', $queryParams) &&
            in_array($queryParams['orderBy'], ['productCount', 'name'])
        ) {
            // fetch paginated
            $result = $this->categoryService->fetchPaginatedCategories($queryParams);
        } else {
            $result = $this->categoryService->fetchAll();
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchById(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $arrayCategory = $this->entityManager->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->select('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getArrayResult();


        if(sizeof($arrayCategory) < 1) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($arrayCategory));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $category = $this->entityManager->find(Category::class, $id);

        if(! $category) {
            return $response->withStatus(404);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $successMessage = [
            'message' => 'Category deleted successfully',
            'deletedCategoryId' => $id,
        ];

        $response->getBody()->write(json_encode($successMessage));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function update(Request $request, Response $response, array $args): Response {
        // get and validate data FIRST
        $data = $this->requestValidatorFactory->make(CreateCategoryRequestValidator::class)
            ->validate($request->getParsedBody());

        $id = (int) $args['id'];

        /** @var Category $category */
        $category = $this->entityManager->find(Category::class, $id);

        if(! $category) {
            return $response->withStatus(404);
        }

        // real update
        $category->setName($data['name']);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $message = [
            'message' => 'category updated successfully!',
            'updatedCategoryId' => $id
        ];

        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}