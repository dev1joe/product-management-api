<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Category;
use Doctrine\ORM\EntityManager;

class CategoryService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function create(array $data): Category {
        $category = new Category();
        $category->setName($data['name']);
        $category->setProductCount(0);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function fetchAll(): array{
        return $this->entityManager->getRepository(Category::class)
            ->createQueryBuilder('c')->select('c')->getQuery()->getArrayResult();
    }

    /**
     * assumes that query parameters exit and validated
     * @param array $queryParams
     * @return array
     */
    public function fetchPaginatedCategories(array $queryParams): array {
        return $this->entityManager->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.' . $queryParams['orderBy'], $queryParams['orderDir'])
            ->getQuery()
            ->getArrayResult();
    }

    public function fetchCategoryNames(): array {
        return $this->entityManager->getRepository(Category::class)->createQueryBuilder('c')
            ->select('c.id', 'c.name')->getQuery()->getArrayResult();
    }

    //TODO: recalculate connections
    // count how many products associated with each category and check if category->productCount is wrong or not
    // if wrong change it
}