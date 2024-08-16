<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Address;
use App\Entities\Warehouse;
use Doctrine\ORM\EntityManager;

class WarehouseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly AddressService $addressService,
    ){
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
     */
    public function create(array $data): Warehouse {
        // address creation
        $address = $this->addressService->create($data);

        // warehouse creation
        $warehouse = new Warehouse();
        $warehouse->setName($data['name']);
        $warehouse->setAddress($address);

        // entityManager
        $this->entityManager->persist($address);
        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return $warehouse;
    }

}