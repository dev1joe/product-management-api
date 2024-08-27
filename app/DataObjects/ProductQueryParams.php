<?php
declare(strict_types=1);

namespace App\DataObjects;

use App\Enums\QueryOrderDirection;

class ProductQueryParams
{
    public readonly int $page;
    public readonly int $limit;
    public readonly string $orderBy;
    public readonly string $orderDir;
    public readonly ?int $categoryId;
    public readonly ?int $minPriceInCents;
    public readonly ?int $maxPriceInCents;

    //TODO: refactor default values to environment variables / constants
    public function __construct(array $query) {
        $this->page = (array_key_exists('page', $query))? (int) $query['page'] : 1;
        $this->limit = (array_key_exists('limit', $query))? (int) $query['limit'] : 10;
        $this->orderBy = (array_key_exists('orderBy', $query))? $query['orderBy'] : 'id'; //TODO: make it dateCreated when the attributes is introduced
        $this->orderDir = (array_key_exists('orderDir', $query))? $query['orderDir'] : 'desc';
        $this->categoryId = (array_key_exists('category', $query))? (int) $query['category'] : null;


        $minPrice = (array_key_exists('minPrice', $query))? (int) $query['minPrice'] : null;
        $this->minPriceInCents = ($minPrice)? $minPrice * 100 : null;

        $maxPrice = (array_key_exists('maxPrice', $query))? (int) $query['maxPrice'] : null;
        $this->maxPriceInCents = ($maxPrice)? $maxPrice * 100 : null;
    }
}