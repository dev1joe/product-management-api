<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\AddressQueryParams;
use App\DataObjects\QueryParams;
use App\Entities\Address;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;

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

    protected function applyFilters(QueryBuilder $query, QueryParams $params): QueryBuilder
    {
        /** @var AddressQueryParams $params */

        if($params->country) {
            $query->where('r.country LIKE :country')->setParameter('country', $params->country);
        }

        if($params->governorate) {
            $query->andWhere('r.governorate LIKE :governorate')->setParameter('governorate', $params->governorate);
        }

        if($params->district) {
            $query->andWhere('r.district LIKE :district')->setParameter('district', $params->district);
        }

        if($params->street) {
            $query->andWhere('r.street LIKE :street')->setParameter('street', $params->street);
        }

        if($params->building) {
            $query->andWhere('r.building LIKE :building')->setParameter('building', $params->building);
        }

        return $query;
    }

    /**
     * a simple create function that assumes the data is already validated and all necessary fields are present
     * @param array $data
     * @return Address
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(array $data): Address {
        $address = new Address();
        $address->setCountry($data['country']);
        $address->setGovernorate($data['governorate']);
        $address->setDistrict($data['district']);

        if(array_key_exists('street', $data)) {
            $address->setStreet($data['street']);
        }

        if(array_key_exists('building', $data)) {
            $address->setBuilding($data['building']);
        }

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
     * @param int $id
     * @param array $data
     * @return Address
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function update(int $id, array $data): Address {
        $address = $this->entityManager->getRepository(Address::class)->find($id);

        if(! $address) {
            throw new EntityNotFoundException('Address Not Found');
        }

        if(isset($data['country'])) {
            $address->setCountry($data['country']);
        }

        if(isset($data['governorate'])) {
            $address->setGovernorate($data['governorate']);
        }

        if(isset($data['district'])) {
            $address->setDistrict($data['district']);
        }

        if(isset($data['street'])) {
            $address->setStreet($data['street']);
        }

        if(isset($data['building'])) {
            $address->setBuilding($data['building']);
        }

        if(isset($data['floor'])) {
            $address->setFloor($data['floor']);
        }

        if(isset($data['apartment'])) {
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