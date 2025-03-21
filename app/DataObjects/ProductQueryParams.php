<?php
declare(strict_types=1);

namespace App\DataObjects;

class ProductQueryParams extends QueryParams
{
    public ?int $categoryId = null;
    public ?int $manufacturerId = null;
    public ?int $minPriceInCents = null;
    public ?int $maxPriceInCents = null;
    public ?float $rating = null;

    public function __construct(array $query) {
        parent::__construct($query);

        // TODO: what if category = string ?
        $this->categoryId = (isset($query['category']))? (int) $query['category'] : null;

        $this->manufacturerId = (isset($query['manufacturer']))? (int) $query['manufacturer'] : null;

        $this->rating = (isset($query['rating']))? (float) $query['rating'] : null;

        $minPrice = (isset($query['minPrice']))? (int) $query['minPrice'] : null;
        $this->minPriceInCents = ($minPrice)? $minPrice * 100 : null;

        $maxPrice = (isset($query['maxPrice']))? (int) $query['maxPrice'] : null;
        $this->maxPriceInCents = ($maxPrice)? $maxPrice * 100 : null;

        return $this;
    }
}