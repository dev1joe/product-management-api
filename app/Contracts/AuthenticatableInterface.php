<?php
declare(strict_types=1);

namespace App\Contracts;

interface AuthenticatableInterface
{
    public function getId(): int;
    public function getEmail(): string;
    public function getPassword(): string;
}