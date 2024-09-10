<?php
declare(strict_types=1);

namespace App\Contracts;

use App\DataObjects\QueryParamsObject;

interface QueryValidatorInterface
{
    //TODO: using DTOs is much accurate
    // it is key errors tolerant, but has no tolerance for values
    // values for the orderBy key are entities attributes that are mentioned in DB queries
    // which are case sensitive
    public function validate(QueryParamsObject $query): QueryParamsObject;
}