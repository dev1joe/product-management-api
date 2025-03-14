<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Inventory;
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

    public function queryAll(): QueryBuilder
    {
        return $this->entityManager->getRepository(Inventory::class)
            ->createQueryBuilder('r')
            ->select('r', 'p', 'w')
            ->leftJoin('r.product', 'p')
            ->leftJoin('r.warehouse', 'w');
    }

    public function create(array $data): Inventory {
        $inventory = new Inventory();
        $inventory->setProduct($data['product']);
        $inventory->setWarehouse($data['warehouse']);
        $inventory->setQuantity((int) $data['quantity']);

        $restock = (array_key_exists('lastrestock', $data))? $data['lastrestock'] : new \DateTime();
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