<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Address;
use App\Entities\Warehouse;
use Doctrine\ORM\EntityManager;

class AddressService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function create(array $data): Warehouse {
        $warehouse = new Warehouse();
        $warehouse->setName($data['name']);

        if(array_key_exists('address_id', $data)) {
            $address = $data['address_id'];
        } else {
            $address = new Address();

            $address->setCountry($data['country']);
            $address->setGovernorate($data['governorate']);
            $address->setDistrict($data['district']);
            $address->setStreet($data['street']);
            $address->setBuilding($data['building']);

            $this->entityManager->persist($address);

        }

        $warehouse->setAddress($address);

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return $warehouse;
    }

    public function fetchAllAddresses(): array {
        return $this->entityManager->getRepository(Address::class)->createQueryBuilder('a')
            ->select('a')->getQuery()->getArrayResult();
    }

    /**
     * @return array of addresses (each address has an id and details string)
     */
    public function fetchAllIdsDetails(): array {
        $addresses =  $this->entityManager->getRepository(Address::class)->createQueryBuilder('a')
            ->select()->getQuery()->getResult();

        $result = [];

        /** @var Address $address */
        foreach($addresses as $address) {
            //TODO: abstract to address object function WITH VALIDATION FOR NULL VALUES
            $details = $address->getCountry() . ", " . $address->getGovernorate() . ", " . $address->getDistrict() . ", street " . $address->getStreet() . ", building " . $address->getBuilding();

            $result[] = ['id' => $address->getId(), 'details' => $details];
        }

        return $result;
    }
}