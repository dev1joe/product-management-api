<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuthenticatableInterface;
use App\Contracts\AuthServiceInterface;
use App\Entities\Customer;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;

class AuthService implements AuthServiceInterface
{
    private ?AuthenticatableInterface $user = null;

    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly CustomerService $customerService,
    ){
    }

    /**
     * @return AuthenticatableInterface|null the customer persisted in the session super-global
     */
    public function customer(): ?AuthenticatableInterface
    {
        if($this->user !== null) {
            return $this->user;
        }

        $customerId = $_SESSION['customer'] ?? null;

        if(! $customerId) {
            return null;
        }

        /** @var ?Customer $customer */
        $customer = $this->entityManager->getRepository(Customer::class)->find($customerId);

        if(! $customer) {
            return null;
        }

        $this->user = $customer;
        return $this->user;
    }

    public function attemptLogIn($data): bool {
        // check credentials
        $customer = $this->customerService->fetchByEmail($data['email']);

        if(! $customer || ! $this->verifyPassword($customer, $data['password'])) {
            return false;
        }

        // regenerate session id for security reasons
        session_regenerate_id();

        // save customer in session
        $_SESSION['customer'] = $customer->getId();
        $this->user = $customer;

        return true;
    }

    public function verifyPassword(AuthenticatableInterface $customer, string $password): bool{
        return password_verify($password, $customer->getPassword());
    }

    public function logOut(): void {
        unset($_SESSION['customer']);
        $this->user = null;
    }
}