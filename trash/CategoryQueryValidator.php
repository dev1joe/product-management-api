<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\Contracts\QueryValidatorInterface;
use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;

class CategoryQueryValidator implements QueryValidatorInterface
{
    public function validate(QueryParams $params): QueryParams
    {
        // if orderBy then (require orderDir with orderBy) & (validate orderBy and orderDir)
        if(! is_string($params->orderBy)) {
            throw new ValidationException(['orderBy' => 'Invalid Type Error: orderBy has to be string']);
        }

        if($params->orderBy) {
            // TODO: include all the attributes of the Category entity + do for other entities
            $allowedAttributes = ['updatedat', 'createdat', 'name', 'productcount', 'id'];
            if(! in_array(strtolower($params->orderBy), $allowedAttributes, true)) {
                throw new ValidationException(['orderBy' => 'Invalid Value Error. Allowed values: ' . implode(', ', $allowedAttributes)]);
            }
        }

        if(! is_string($params->orderDir)) {
            throw new ValidationException(['orderDir' => 'Invalid Type Error: orderDir has to be string']);
        }

        if($params->orderDir) {
            $orderDirections = ['asc', 'desc'];
            if(! in_array(strtolower($params->orderDir), $orderDirections)) {
                throw new ValidationException(['orderDir' => 'Invalid Value Error: Allowed values: ' . implode(', ', $orderDirections)]);
            }
        }

        if(is_string($params->limit) && ! ctype_digit($params->limit)) {
            throw new ValidationException(['limit' => 'Invalid Type Error: limit has to be integer']);
        } else {
            $params->limit = (int) $params->limit;
        }

        if($params->limit < 1) {
            throw new ValidationException(['limit' => 'Invalid Value Error: limit has to be greater than 1' . ' ' . $params->limit]);
        }

        if(is_string($params->page) && ! ctype_digit($params->page)) {
            throw new ValidationException(['page' => 'Invalid Type Error: page has to be integer']);
        } else {
            $params->page = (int) $params->page;
        }

        if($params->page < 1) {
            throw new ValidationException(['page' => 'Invalid Value Error: page has to be greater than 1']);
        }

        return $params;
    }
}