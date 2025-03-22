<?php
declare(strict_types=1);

namespace App\Contracts;

use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;

interface QueryValidatorInterface
{
    /**
     * @param QueryParams $params
     * @return QueryParams
     * @throws ValidationException
     */
    public function validate(QueryParams $params): QueryParams;
}