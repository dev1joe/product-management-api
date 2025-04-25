<?php
declare(strict_types=1);

namespace App\DataObjects;

class ProductQueryParams extends QueryParams
{
    public string|int|null $categoryId = null;
    public string|int|null $manufacturerId = null;
    public string|int|null $minPriceInCents = null;
    public string|int|null $maxPriceInCents = null;
    public string|float|null $rating = null;

    public function __construct(array $params) {
        parent::__construct($params);

        $this->categoryId = (isset($params['category']))? $params['category'] : null;

        $this->manufacturerId = (isset($params['manufacturer']))? $params['manufacturer'] : null;

        $this->rating = (isset($params['rating']))? $params['rating'] : null;

        $this->minPriceInCents = (isset($params['minPrice']))? $params['minPrice'] : null;

        $this->maxPriceInCents = (isset($params['maxPrice']))? $params['maxPrice'] : null;
    }
}