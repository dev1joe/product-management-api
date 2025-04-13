<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\Contracts\QueryValidatorInterface;
use App\DataObjects\ProductQueryParams;
use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;
use App\Services\CurrencyService;

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
//            if (!ctype_digit($params->categoryId)) {
//                $errors['categoryId'][] = 'Invalid Type Error: categoryId has to be integer';
//            } else {
//                $params->categoryId = (int) $params->categoryId;
//            }

            if(ctype_digit($params->categoryId)) {
                $num = (int) $params->categoryId;

                if($num == 0) {
                    $errors['categoryId'][] = 'Invalid Value Error: categoryId has to be greater than 0';
                } else {
                    $params->categoryId = $num;
                }

            } else {
                $errors['categoryId'][] = 'Invalid Type Error: categoryId has to be integer';
            }
        }

        if($params->manufacturerId !== null) {
            if (ctype_digit($params->manufacturerId)) {
                $num = (int) $params->manufacturerId;

                if($num == 0) {
                    $errors['manufacturerId'][] = 'Invalid Value Error: manufacturerId has to be greater than 0';
                } else {
                    $params->manufacturerId = $num;
                }
            } else {
                $errors['manufacturerId'][] = 'Invalid Type Error: manufacturerId has to be integer';
            }
        }

        if($params->rating !== null) {
            if(is_numeric($params->rating)) {
                $num = (float) $params->rating;
                if($num < 0) {
                    $errors['rating'][] = 'Invalid Value Error: rating has to be positive';
                } else {
                    $params->rating = (float) $params->rating;
                }
            } else {
                $errors['rating'][] = 'Invalid Type Error: rating has to be a number';
            }
        }

        $currencyService = new CurrencyService();

        if($params->minPriceInCents !== null) {
            if(is_numeric($params->minPriceInCents)) {
                $num = (float) $params->minPriceInCents;

                if($num < 0) {
                    $errors['minPrice'][] = 'Invalid Value Error: minPrice has to be greater than 0';
                } else {
                    $params->minPriceInCents = $currencyService->dollarsToCents($num);
                }
            } else {
                $errors['minPrice'][] = 'Invalid Type Error: minPrice has to be integer';
            }
        }

        if($params->maxPriceInCents !== null) {
            if(is_numeric($params->maxPriceInCents)) {
                $num = (float) $params->maxPriceInCents;

                if($num < 0) {
                    $errors['maxPrice'][] = 'Invalid Value Error: maxPrice has to be greater than 0';
                } else {
                    $params->maxPriceInCents = $currencyService->dollarsToCents($num);
                }
            } else {
                $errors['maxPrice'][] = 'Invalid Type Error: maxPrice has to be integer';
            }
        }

        return $errors;
    }
}