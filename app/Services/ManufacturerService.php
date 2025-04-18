<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\ManufacturerQueryParams;
use App\DataObjects\QueryParams;
use App\Entities\Manufacturer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;

class ManufacturerService extends BaseService
{
    public function __construct(
        public readonly EntityManager $entityManager,
    ){
        parent::__construct(
            $this->entityManager,
            Manufacturer::class
        );
    }

    protected function applyFilters(QueryBuilder $query, QueryParams $params): QueryBuilder {
        /** @var ManufacturerQueryParams $params */

        if($params->name) {
            $query->where('r.name LIKE :name')->setParameter('name', $params->name);
        }

        if($params->email) {
            $query->andWhere('r.email LIKE :email')->setParameter('email', $params->email);
        }

        return $query;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(int $id, array $data): Manufacturer {
        $manufacturer = $this->entityManager->getRepository(Manufacturer::class)->find($id);

        if(! $manufacturer) {
            throw new EntityNotFoundException('Manufacturer Not Found');
        }

        if(isset($data['name'])) {
            $manufacturer->setName($data['name']);
        }

        if(isset($data['email'])) {
            $manufacturer->setEmail($data['email']);
        }

        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();

        return $manufacturer;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(array $data): Manufacturer {
        $manufacturer = new Manufacturer();
        $manufacturer->setName($data['name']);
        $manufacturer->setEmail($data['email']);
        $manufacturer->setProductCount(0);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();

        return $manufacturer;
    }
}