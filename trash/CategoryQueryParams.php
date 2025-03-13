<?php
declare(strict_types=1);

namespace App\DataObjects;

class CategoryQueryParams extends QueryParams
{
    public function __construct(array $queryParams)
    {
        parent::__construct($queryParams);
    }
}