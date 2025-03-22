<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\Contracts\QueryValidatorInterface;
use App\DataObjects\ProductQueryParams;
use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;

class ProductQueryValidator extends BaseQueryValidator
{
    public function __construct(array $allowedAttributes)
    {
        parent::__construct($allowedAttributes);
    }

    protected function validateFilterParams(QueryParams $params): array
    {
        $errors = [];

        /** @var ProductQueryParams $params */

        if($params->categoryId !== null) {
            if (is_string($params->categoryId) && !ctype_digit($params->categoryId)) {
                $errors['categoryId'][] = 'Invalid Type Error: categoryId has to be integer';
            } else {
                $params->categoryId = (int) $params->categoryId;
            }
        }

        if($params->manufacturerId !== null) {
            if (is_string($params->manufacturerId) && !ctype_digit($params->manufacturerId)) {
                $errors['manufacturerId'][] = 'Invalid Type Error: manufacturerId has to be integer';
            } else {
                $params->manufacturerId = (int) $params->manufacturerId;
            }
        }

        if($params->rating !== null) {
            if (is_string($params->rating) && !ctype_digit($params->rating)) {
                $errors['rating'][] = 'Invalid Type Error: rating has to be a number';
            } else {
                $params->rating = (float) $params->rating;
            }
        }

        if($params->minPriceInCents !== null) {
            if(is_string($params->minPriceInCents) && !ctype_digit($params->minPriceInCents)) {
                $errors['minPrice'][] = 'Invalid Type Error: minPrice has to be integer';
            } else {
                $params->minPriceInCents = (int) $params->minPriceInCents;

                if($params->minPriceInCents < 0) {
                    $errors['minPrice'][] = 'Invalid Value Error: minPrice has to be greater than 0';
                }
            }
        }

        if($params->maxPriceInCents !== null) {
            if(! ctype_digit($params->maxPriceInCents)) {
                $errors['maxPrice'][] = 'Invalid Type Error: maxPrice has to be integer';
            } else {
                $params->maxPriceInCents = (int) $params->maxPriceInCents;

                if($params->maxPriceInCents < 0) {
                    $errors['maxPrice'][] = 'Invalid Value Error: maxPrice has to be greater than 0';
                }
            }
        }

        return $errors;
    }
}