<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\ProductQueryParams;
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

    public function fetchPaginatedProducts(ProductQueryParams $params): array {
        // calculate offset
        $offset = ($params->page - 1) * $params->limit;

        // execute query and return result
        $query =  $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('p', 'c')
            ->leftJoin('p.category' , 'c');

        if($params->categoryId) {
            $query->where('c.id = :id')->setParameter('id', $params->categoryId);
        }

        if($params->minPriceInCents) {
            $query->andWhere('p.unitPriceCents > :min')->setParameter('min', $params->minPriceInCents);
        }

        if($params->maxPriceInCents) {
            $query->andWhere('p.unitPriceCents < :max')->setParameter('max', $params->maxPriceInCents);
        }

        $query->setFirstResult($offset)
            ->setMaxResults($params->limit)
            ->orderBy("p.".$params->orderBy, $params->orderDir);

        return $query->getQuery()->getArrayResult();
    }
}