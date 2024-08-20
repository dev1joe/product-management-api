<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuthenticatableInterface;
use App\Contracts\AuthServiceInterface;
use App\Entities\Administrator;
use App\Entities\Customer;
use App\Enums\UserType;
use App\Middlewares\AdminAuthorizationMiddleware;
use App\Middlewares\CustomerAuthorizationMiddleware;
use Doctrine\ORM\EntityManager;

class AuthService implements AuthServiceInterface
{
    private ?AuthenticatableInterface $user = null;
    private ?UserType $userType = null;

    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly CustomerService $customerService,
        private readonly AdminService $adminService
    ){
    }

    /**
     * @return AuthenticatableInterface|null the customer persisted in the session super-global
     */
    public function getAuthenticatedUser(): ?AuthenticatableInterface
    {
        if($this->user !== null) {
            return $this->user;
        }

        $userId = $_SESSION['userId'] ?? null;
        $userType = $_SESSION['userType'] ?? null;

        if(! $userId || ! $userType) {
            return null;
        }

        $className = ($userType === UserType::Customer->value)? Customer::class : Administrator::class;
        $user = $this->entityManager->getRepository($className)->find($userId);

        if(! $user) {
            return null;
        }

        $this->user = $user;
        return $this->user;
    }

    public function getUserType(): ?UserType {
        if($this->userType !== null) {
            return $this->userType;
        }

        $user = $this->getAuthenticatedUser();

        if(! $user) {
            return null;
        }

        if($user instanceof Customer) {
            return UserType::Customer;
        } else {
            return UserType::Admin;
        }
    }

    /**
     * Saves the customer entity for later use. <br>
     * @return boolean Whether the LogIn process was successful or not.
     */
    public function attemptLogIn(array $data, UserType $userType): bool {
        // check credentials
        if($userType === UserType::Customer) {
            $user = $this->customerService->fetchByEmail($data['email']);
            $this->userType = UserType::Customer;
        } else {
            $user = $this->adminService->fetchByEmail($data['email']);
            $this->userType = UserType::Admin;
        }


        if(! $user || ! $this->verifyPassword($user, $data['password'])) {
            return false;
        }

        // regenerate session id for security reasons
        session_regenerate_id();

        // save customer in session
        $_SESSION['userType'] = $this->userType->value;
        $_SESSION['userId'] = $user->getId();
        $this->user = $user;

        return true;
    }

    public function verifyPassword(AuthenticatableInterface $user, string $password): bool{
        return password_verify($password, $user->getPassword());
    }

    /**
     * it simply logs out the customer
     * this function will not be accessed unless the application makes sure that it's dependencies are available
     * by `CustomerAuthorizationMiddleware` & `AdminAuthorizationMiddleware`
     * @see CustomerAuthorizationMiddleware, AdminAuthorizationMiddleware
     */
    public function logOut(): void {
        unset($_SESSION['userType']);
        unset($_SESSION['userId']);
        $this->user = null;
        $this->userType = null;
    }
}