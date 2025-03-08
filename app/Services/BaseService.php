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

    public function fetchPaginated(QueryParams $queryParams): array {

        $query = $this->queryAll();

        // if the page is not set by the user, it'll be set to the default value,
        // so no need for validation at that point
        $query->setFirstResult($queryParams->limit * ($queryParams->page - 1));
        $query->setMaxResults($queryParams->limit);

        $query->orderBy('r.' . $queryParams->orderBy, $queryParams->orderDir);

        $data = $query->getQuery()->getArrayResult();

        $metadata = [];
        $metadata['orderBy'] = $queryParams->orderBy;
        $metadata['orderDir'] = $queryParams->orderDir;
        $metadata['page'] = $queryParams->page;
        $metadata['limit'] = $queryParams->limit;
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

//        return [
//            'data' => $data,
//            'metadata' => [
//                'status' => 'success',
//                'id' => $id
//            ]
//        ];
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