<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\QueryParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\QueryBuilder;

class BaseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly string $className,
    ){
    }

    /**
     * return the Base Query
     * @return QueryBuilder
     */
    public function queryAll(): QueryBuilder {
        return $this->entityManager->getRepository($this->className)
            ->createQueryBuilder('r') // r for Resource
            ->select('r');
    }

    /**
     * Just use the Base Query from "queryAll" function and return the array result
     * @return array
     */
    public function fetchAll(): array {
        return $this->queryAll()->getQuery()->getArrayResult();
    }

    public function fetchPaginated(?QueryParams $queryParams = null): array {
        $metadata = [];

        if($queryParams) {
            $query = $this->queryAll();

            if($queryParams->orderBy) {
                $query->orderBy('r.' . $queryParams->orderBy, $queryParams->orderDir);

                $metadata['orderBy'] = $queryParams->orderBy;
                $metadata['orderDir'] = $queryParams->orderDir;
            }

            if($queryParams->limit) {
                $query->setFirstResult($queryParams->limit * ($queryParams->page - 1));
                $query->setMaxResults($queryParams->limit);

                $metadata['page'] = $queryParams->page;
                $metadata['limit'] = $queryParams->limit;
            }

            $data = $query->getQuery()->getArrayResult();
        } else {
            $data = $this->fetchAll();
        }

        $metadata['totalItems'] = sizeof($data);

        return [
            'data' => $data,
            'metadata' => $metadata
        ];
    }

    public function fetchById(int $id): array
    {
        return $this->queryAll()
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws EntityNotFoundException
     */
    public function delete(int $id): void {
        $entity = $this->entityManager->getRepository($this->className)->find($id);
        if(! $entity) {
            throw new EntityNotFoundException('Entity Not Found');
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}