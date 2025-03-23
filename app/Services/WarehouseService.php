<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\QueryParams;
use App\Entities\Address;
use App\Entities\Warehouse;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;

class WarehouseService extends BaseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
        parent::__construct(
            $this->entityManager,
            Warehouse::class
        );
    }

    public function createWithAddress(string $name, Address $address) {
        $warehouse = new Warehouse();
        $warehouse->setName($name);
        $warehouse->setAddress($address);

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return $warehouse;
    }

    /**
     * a simple create function that assumes the data is already validated and all necessary fields are present
     * @return Warehouse
     * @throws ORMException
     */
    public function create(array $data): Warehouse {
        // address creation
        // $address = $this->addressService->create($data);

        // warehouse creation
        $warehouse = new Warehouse();
        $warehouse->setName($data['name']);
        $warehouse->setAddress($data['address']);

        // entityManager
        // $this->entityManager->persist($address);
        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return $warehouse;
    }

    public function queryAll(?QueryParams $params = null): QueryBuilder
    {
        $query = $this->entityManager->getRepository(Warehouse::class)
            ->createQueryBuilder('r') // r for Resource
            ->select('r', 'a')
            ->leftJoin('r.address', 'a');

        if($params) {
            return $this->applyFilters($query, $params);
        }

        return $query;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(int $id, array $data): Warehouse {
        $warehouse = $this->entityManager->getRepository(Warehouse::class)->find($id);
        if(! $warehouse) {
            throw new EntityNotFoundException('Warehouse Not Found');
        }

        if(isset($data['name'])) {
            $warehouse->setName($data['name']);
        }

        if(isset($data['address'])) {
            $warehouse->setAddress($data['address']);
        }

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return $warehouse;
    }

}