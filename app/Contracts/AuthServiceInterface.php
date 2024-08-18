<?php
declare(strict_types=1);

namespace App\Contracts;

interface AuthServiceInterface
{
    /**
     * @return AuthenticatableInterface|null The customer persisted in the session super-global
     */
    public function customer(): ?AuthenticatableInterface;

    /**
     * Saves the customer entity for later use. <br>
     * @return boolean Whether the LogIn process was successful or not.
     */
    public function attemptLogIn(array $data): bool;

    /**
     * @return boolean Whether the given password matches the stored password or not
     */
    public function verifyPassword(AuthenticatableInterface $customer, string $password): bool;

    /** it simply logs out the customer */
    public function logOut(): void;
}