<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\Contracts\QueryValidatorInterface;
use App\DataObjects\ProductQueryParams;
use App\DataObjects\QueryParamsObject;
use App\Exceptions\ValidationException;

class ProductQueryValidator implements QueryValidatorInterface
{

    public function validate(QueryParamsObject $query): QueryParamsObject
    {
        // TODO: Implement validate() method.
        /** @var ProductQueryParams $query */

        if(! $query->page || $query->page < 0) {
            throw new ValidationException([]);
        }

        if(! $query->limit || $query->limit < 0) {
            throw new ValidationException([]);
        }

        $allowedAttributes = ['createdat', 'updatedat', 'unitpriceincents', 'avgrating'];
        if(! $query->orderBy || ! in_array(strtolower($query->orderBy), $allowedAttributes)) {
            throw new ValidationException([]);
        }

        $orderDirections = ['asc', 'desc'];
        if(! $query->orderDir || ! in_array(strtolower($query->orderDir), $orderDirections)) {
            throw new ValidationException([]);
        }

        if($query->categoryId && $query->categoryId < 0) {
            throw new ValidationException([]);
        }

        if($query->minPriceInCents && $query->minPriceInCents < 0) {
            throw new ValidationException([]);
        }

        return $query;
    }
}