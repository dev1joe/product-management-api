<?php
declare(strict_types=1);

namespace App\Contracts;

interface RequestValidatorInterface extends ValidatorInterface
{
    public function validate(array $data): array;
}