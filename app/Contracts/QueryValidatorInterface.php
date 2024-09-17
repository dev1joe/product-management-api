<?php
declare(strict_types=1);

namespace App\Contracts;

use App\DataObjects\QueryParamsObject;

interface QueryValidatorInterface
{
    public function validate(QueryParamsObject $query): QueryParamsObject;
}