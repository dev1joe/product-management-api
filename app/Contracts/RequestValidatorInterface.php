<?php
declare(strict_types=1);

namespace App\Contracts;

use App\Exceptions\ValidationException;

interface RequestValidatorInterface extends ValidatorInterface
{
    /**
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validate(array $data): array;
}