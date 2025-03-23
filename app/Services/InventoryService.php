<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\InventoryQueryParams;
use App\DataObjects\QueryParams;
use App\Entities\Inventory;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;

class InventoryService extends BaseService
{
    public function __construct(
        private readonly EntityManager $entityManager
    ){
        parent::__construct(
            $this->entityManager,
            Inventory::class
        );
    }

    public function queryAll(?QueryParams $params = null): QueryBuilder
    {
        $query = $this->entityManager->getRepository(Inventory::class)
            ->createQueryBuilder('r')
            ->select('r', 'p', 'w')
            ->leftJoin('r.product', 'p')
            ->leftJoin('r.warehouse', 'w');

        if($params) {
            return $this->applyFilters($query, $params);
        }

        return $query;
    }

    protected function applyFilters(QueryBuilder $query, QueryParams $params): QueryBuilder
    {
        /** @var InventoryQueryParams $params */

        if($params->warehouseId) {
            $query->andWhere('w.id = ?0')->setParameter(0, $params->warehouseId);
        }

        if($params->productId) {
            $query->andWhere('p.id = ?1')->setParameter(1, $params->productId);
        }

        if($params->quantity) {
            $query->andWhere('r.quantity = :quantity')->setParameter('quantity', $params->quantity);
        }

        return $query;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(array $data): Inventory {
        $inventory = new Inventory();
        $inventory->setProduct($data['product']);
        $inventory->setWarehouse($data['warehouse']);
        $inventory->setQuantity((int) $data['quantity']);

        $restock = (array_key_exists('lastrestock', $data))? $data['lastrestock'] : new DateTime();
        $inventory->setLastRestock($restock);

        $this->entityManager->persist($inventory);
        $this->entityManager->flush();

        return $inventory;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(int $id, array $data): Inventory {
        $inventory = $this->entityManager->find(Inventory::class, $id);

        if(! $inventory) {
            throw new EntityNotFoundException('Inventory Not Found');
        }

        if(isset($data['product'])) {
            $inventory->setProduct($data['product']);
        }

        if(isset($data['warehouse'])) {
            $inventory->setWarehouse($data['warehouse']);
        }

        if(isset($data['quantity'])) {
            $inventory->setQuantity((int) $data['quantity']);
        }

        if(isset($data['lastrestock'])) {
            $inventory->setLastRestock($data['lastrestock']);
        }

        $this->entityManager->persist($inventory);
        $this->entityManager->flush();

        return $inventory;
    }
}