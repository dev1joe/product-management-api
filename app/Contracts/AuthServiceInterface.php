<?php
declare(strict_types=1);

namespace App\Contracts;

use App\Enums\UserType;
use App\Middlewares\AdminAuthorizationMiddleware;
use App\Middlewares\CustomerAuthorizationMiddleware;

interface AuthServiceInterface
{
    /**
     * @return AuthenticatableInterface|null The customer persisted in the session super-global
     */
    public function getAuthenticatedUser(): ?AuthenticatableInterface;

    public function getUserType(): ?UserType;

    /**
     * Saves the customer entity for later use. <br>
     * @return boolean Whether the LogIn process was successful or not.
     */
    public function attemptLogIn(array $data, UserType $userType): bool;

    /**
     * @return boolean Whether the given password matches the stored password or not
     */
    public function verifyPassword(AuthenticatableInterface $user, string $password): bool;

    /**
     * it simply logs out the customer
     * this function will not be accessed unless the application makes sure that it's dependencies are available
     * by `CustomerAuthorizationMiddleware` & `AdminAuthorizationMiddleware`
     * @see CustomerAuthorizationMiddleware, AdminAuthorizationMiddleware
     */
    public function logOut(): void;
}