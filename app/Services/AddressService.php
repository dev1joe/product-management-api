<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Address;
use App\Entities\Warehouse;
use Doctrine\ORM\EntityManager;

class AddressService extends BaseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
        parent::__construct(
            $this->entityManager,
            Address::class
        );
    }

    /**
     * a simple create function that assumes the data is already validated and all necessary fields are present
     * @return Address
     */
    public function create(array $data): Address {
        $address = new Address();
        $address->setCountry($data['country']);
        $address->setGovernorate($data['governorate']);
        $address->setDistrict($data['district']);
        $address->setStreet($data['street']);
        $address->setBuilding($data['building']);

        if(array_key_exists('floor', $data)) {
            $address->setFloor($data['floor']);
        }

        if(array_key_exists('apartment', $data)) {
            $address->setApartment($data['apartment']);
        }

        $this->entityManager->persist($address);
        $this->entityManager->flush();

        return $address;
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