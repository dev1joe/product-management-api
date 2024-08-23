<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Product;
use Doctrine\ORM\EntityManager;

class ProductService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function fetchAll(): array {
        return $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')->select('p', 'c')->leftJoin('p.category', 'c')
            ->getQuery()->getArrayResult();
    }

    public function fetchPaginatedProducts(int $page, int $limit): array {
        // calculate offset
        $offset = ($page - 1) * $limit;

        // execute query and return result
        return $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('p', 'c')
            ->leftJoin('p.category' , 'c')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}