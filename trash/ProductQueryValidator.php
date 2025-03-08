<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\Contracts\QueryValidatorInterface;
use App\DataObjects\ProductQueryParams;
use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;

class ProductQueryValidator implements QueryValidatorInterface
{

    public function validate(QueryParams $query): QueryParams
    {
        /** @var ProductQueryParams $query */

        if(! is_string($query->orderBy)) {
            throw new ValidationException(['orderBy' => 'Invalid Type Error: orderBy has to be string']);
        }

        $allowedAttributes = ['createdat', 'updatedat', 'unitpriceincents', 'avgrating', 'id'];
        if(! $query->orderBy || ! in_array(strtolower($query->orderBy), $allowedAttributes)) {
            throw new ValidationException(['orderBy' => 'Invalid Value Error. Allowed values: ' . implode(', ', $allowedAttributes)]);
        }

        if(! is_string($query->orderDir)) {
            throw new ValidationException(['orderDir' => 'Invalid Type Error: orderDir has to be string']);
        }

        $orderDirections = ['asc', 'desc'];
        if(! $query->orderDir || ! in_array(strtolower($query->orderDir), $orderDirections)) {
            throw new ValidationException(['orderDir' => 'Invalid Type Error: orderDir has to be string']);
        }

        if(is_string($query->limit) && ! ctype_digit($query->limit)) {
            throw new ValidationException(['limit' => 'Invalid Type Error: limit has to be integer']);
        } else {
            $query->limit = (int) $query->limit;
        }

        if($query->limit < 1) {
            throw new ValidationException(['limit' => 'Invalid Value Error: limit has to be greater than 1' . ' ' . $query->limit]);
        }

        if(is_string($query->page) && ! ctype_digit($query->page)) {
            throw new ValidationException(['page' => 'Invalid Type Error: page has to be integer']);
        } else {
            $query->page = (int) $query->page;
        }

        if($query->page < 1) {
            throw new ValidationException(['page' => 'Invalid Value Error: page has to be greater than 1']);
        }

        if($query->categoryId !== null && $query->categoryId < 0) {
            throw new ValidationException(['categoryId' => 'Invalid Value Error: categoryId has to be greater than 1']);
        }

        if($query->minPriceInCents !== null && $query->minPriceInCents < 0) {
            throw new ValidationException(['minPriceInCents' => 'Invalid Value Error: minimum price must be positive']);
        }

        return $query;
    }
}