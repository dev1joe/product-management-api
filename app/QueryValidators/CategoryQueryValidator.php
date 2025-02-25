<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\Contracts\QueryValidatorInterface;
use App\DataObjects\CategoryQueryParams;
use App\DataObjects\QueryParams;
use App\Exceptions\MissingQueryParamsException;
use App\Exceptions\ValidationException;

class CategoryQueryValidator implements QueryValidatorInterface
{
    public function validate(QueryParams $query): QueryParams
    {
        // if orderBy then (require orderDir with orderBy) & (validate orderBy and orderDir)
        if($query->orderBy) {
            if(! $query->orderDir) {
                throw new MissingQueryParamsException('orderDir');
            }

            $allowedAttributes = ['updatedat', 'createdat', 'name', 'productcount'];
            if(! in_array(strtolower($query->orderBy), $allowedAttributes)) {
                throw new ValidationException([]);
            }

            $orderDirections = ['asc', 'desc'];
            if(! in_array(strtolower($query->orderDir), $orderDirections)) {
                throw new ValidationException([]);
            }
        }

        if($query->limit) {
            // $v->rule('integer', 'limit');

            if($query->limit < 1) {
                throw new ValidationException([]);
            }
        }

        return $query;
    }
}