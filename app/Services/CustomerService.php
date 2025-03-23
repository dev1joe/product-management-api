<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\QueryParams;
use App\Entities\Customer;
use App\Exceptions\MethodNotImplementedException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class CustomerService extends BaseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
        parent::__construct(
            $this->entityManager,
            Customer::class
        );
    }


    /**
     * a simple create function that assumes the data is already validated and all necessary fields are present
     * @return Customer
     */
    public function create(array $data): Customer {
        $customer = new Customer();

        $customer->setFirstName($data['firstName']);
        $customer->setLastName($data['lastName']);
        $customer->setEmail($data['email']);
        $customer->setPassword(password_hash(
            $data['password'],
            PASSWORD_BCRYPT,
            ['cost' => 12]
        ));

        if(array_key_exists('middleName', $data)) {
            $customer->setMiddleName($data['middleName']);
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $customer;
    }

    public function queryAll(?QueryParams $params = null): QueryBuilder
    {
        return $this->entityManager->getRepository(Customer::class)
            ->createQueryBuilder('r')
            ->select('r', 'a')
            ->leftJoin('r.address', 'a');
    }

    public function fetchByEmail(string $email): array {
        return $this->queryAll()
            ->where('r.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getArrayResult();
    }
}