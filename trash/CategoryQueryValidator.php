<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\Contracts\QueryValidatorInterface;
use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;

class CategoryQueryValidator implements QueryValidatorInterface
{
    public function validate(QueryParams $query): QueryParams
    {
        // if orderBy then (require orderDir with orderBy) & (validate orderBy and orderDir)
        if(! is_string($query->orderBy)) {
            throw new ValidationException(['orderBy' => 'Invalid Type Error: orderBy has to be string']);
        }

        if($query->orderBy) {
            // TODO: include all the attributes of the Category entity + do for other entities
            $allowedAttributes = ['updatedat', 'createdat', 'name', 'productcount', 'id'];
            if(! in_array(strtolower($query->orderBy), $allowedAttributes, true)) {
                throw new ValidationException(['orderBy' => 'Invalid Value Error. Allowed values: ' . implode(', ', $allowedAttributes)]);
            }
        }

        if(! is_string($query->orderDir)) {
            throw new ValidationException(['orderDir' => 'Invalid Type Error: orderDir has to be string']);
        }

        if($query->orderDir) {
            $orderDirections = ['asc', 'desc'];
            if(! in_array(strtolower($query->orderDir), $orderDirections)) {
                throw new ValidationException(['orderDir' => 'Invalid Value Error: Allowed values: ' . implode(', ', $orderDirections)]);
            }
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

        return $query;
    }
}