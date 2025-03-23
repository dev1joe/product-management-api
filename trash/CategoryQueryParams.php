<?php
declare(strict_types=1);

namespace App\DataObjects;

class CategoryQueryParams extends QueryParams
{
    public function __construct(array $params)
    {
        parent::__construct($params);
    }
}