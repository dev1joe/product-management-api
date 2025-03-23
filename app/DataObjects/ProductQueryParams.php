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

    public function __construct(array $params) {
        parent::__construct($params);

        $this->categoryId = (isset($params['category']))? $params['category'] : null;

        $this->manufacturerId = (isset($params['manufacturer']))? $params['manufacturer'] : null;

        $this->rating = (isset($params['rating']))? $params['rating'] : null;

        // TODO: use currency service 🔴
        $minPrice = (isset($params['minPrice']))? $params['minPrice'] : null;
        $this->minPriceInCents = ($minPrice)? $minPrice * 100 : null;

        $maxPrice = (isset($params['maxPrice']))? $params['maxPrice'] : null;
        $this->maxPriceInCents = ($maxPrice)? $maxPrice * 100 : null;
    }
}