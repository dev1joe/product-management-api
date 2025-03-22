<?php
declare(strict_types=1);

namespace App\DataObjects;

class ProductQueryParams extends QueryParams
{
    public mixed $categoryId = null;
    public mixed $manufacturerId = null;
    public mixed $minPriceInCents = null;
    public mixed $maxPriceInCents = null;
    public mixed $rating = null;

    public function __construct(array $query) {
        parent::__construct($query);

        // TODO: what if category = string ?
        $this->categoryId = (isset($query['category']))? $query['category'] : null;

        $this->manufacturerId = (isset($query['manufacturer']))? $query['manufacturer'] : null;

        $this->rating = (isset($query['rating']))? $query['rating'] : null;

        // TODO: use currency service 🔴
        $minPrice = (isset($query['minPrice']))? $query['minPrice'] : null;
        $this->minPriceInCents = ($minPrice)? $minPrice * 100 : null;

        $maxPrice = (isset($query['maxPrice']))? $query['maxPrice'] : null;
        $this->maxPriceInCents = ($maxPrice)? $maxPrice * 100 : null;

        return $this;
    }
}