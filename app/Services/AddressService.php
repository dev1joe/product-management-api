<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Address;
use Doctrine\ORM\EntityManager;

class AddressService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function fetchAllAddresses(): array {
        return $this->entityManager->getRepository(Address::class)->createQueryBuilder('a')
            ->select('a')->getQuery()->getArrayResult();
    }

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