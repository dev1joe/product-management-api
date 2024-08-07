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

    public function fetchAll(): array{
        return $this->entityManager->getRepository(Category::class)
            ->createQueryBuilder('c')->select('c')->getQuery()->getArrayResult();
    }

    public function fetchCategoryNames(): array {
        return $this->entityManager->getRepository(Category::class)->createQueryBuilder('c')
            ->select('c.id', 'c.name')->getQuery()->getArrayResult();
    }
}