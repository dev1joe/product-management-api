<?php
declare(strict_types=1);

namespace App\Contracts;

use App\DataObjects\QueryParams;

interface QueryValidatorInterface
{
    public function validate(QueryParams $query): QueryParams;
}