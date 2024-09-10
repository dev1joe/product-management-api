<?php
declare(strict_types=1);

namespace App\DataObjects;

class ProductQueryParams extends QueryParamsObject
{
    public ?int $categoryId = null;
    public ?int $minPriceInCents = null;
    public ?int $maxPriceInCents = null;

    //TODO: refactor default values to environment variables / constants
    public function __construct(array $query) {
        $page = (isset($query['page']))? (int) $query['page'] : 1;
        $limit = (isset($query['limit']))? (int) $query['limit'] : 10;
        $orderBy = (isset($query['orderBy']))? $query['orderBy'] : 'updatedAt';
        $orderDir = (isset($query['orderDir']))? $query['orderDir'] : 'desc';

        // although these properties are nullable and I can't override them to not be nullable,
        // I was able to enforce default values, meaning that they will not be null
        parent::__construct([
            'page' => $page,
            'limit' => $limit,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir
        ]);

        $this->categoryId = (isset($query['category']))? (int) $query['category'] : null;


        $minPrice = (isset($query['minPrice']))? (int) $query['minPrice'] : null;
        $this->minPriceInCents = ($minPrice)? $minPrice * 100 : null;

        $maxPrice = (isset($query['maxPrice']))? (int) $query['maxPrice'] : null;
        $this->maxPriceInCents = ($maxPrice)? $maxPrice * 100 : null;
    }
}