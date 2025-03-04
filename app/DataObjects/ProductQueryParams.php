<?php
declare(strict_types=1);

namespace App\DataObjects;

class ProductQueryParams extends QueryParams
{
    public ?int $categoryId = null;
    public ?int $minPriceInCents = null;
    public ?int $maxPriceInCents = null;

    public function __construct(array $query) {
        parent::__construct($query);

        $this->categoryId = (isset($query['category']))? (int) $query['category'] : null;


        $minPrice = (isset($query['minPrice']))? (int) $query['minPrice'] : null;
        $this->minPriceInCents = ($minPrice)? $minPrice * 100 : null;

        $maxPrice = (isset($query['maxPrice']))? (int) $query['maxPrice'] : null;
        $this->maxPriceInCents = ($maxPrice)? $maxPrice * 100 : null;

        return $this;
    }
}