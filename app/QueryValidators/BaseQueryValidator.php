<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\Contracts\QueryValidatorInterface;
use App\DataObjects\QueryParams;
use App\Exceptions\ValidationException;
use Valitron\Validator;

class BaseQueryValidator implements QueryValidatorInterface
{

    public function __construct(
        private readonly array $allowedAttributes,
    ){
    }

    /**
     * @param QueryParams $params
     * @return QueryParams
     * @throws ValidationException
     */
    public function validate(QueryParams $params): QueryParams
    {
        $errors = [];

        $errors = array_merge($errors, $this->validateSortParams($params));
        $errors = array_merge($errors, $this->validatePaginationParams($params));
        $errors = array_merge($errors, $this->validateFilterParams($params));

        if(sizeof($errors) > 0) {
            throw new ValidationException($errors);
        }

        return $params;
    }

    protected function validateSortParams(QueryParams $params): array {
        $errors = [];

        if($params->orderBy) {
            if(! is_string($params->orderBy)) {
                $errors['orderBy'][] = 'Invalid Type Error: orderBy has to be string';
            }

            if(! in_array(strtolower($params->orderBy), $this->allowedAttributes, true)) {
                $errors['orderBy'][] = 'Invalid Value Error. Allowed values: ' . implode(', ', $this->allowedAttributes);
            }
        }


        if($params->orderDir) {
            if(! is_string($params->orderDir)) {
                $errors['orderDir'][] = 'Invalid Type Error: orderDir has to be string';
            }

            $orderDirections = ['asc', 'desc'];
            if(! in_array(strtolower($params->orderDir), $orderDirections)) {
                $errors['orderDir'][] = 'Invalid Value Error: Allowed values: ' . implode(', ', $orderDirections);
                // throw new ValidationException(['orderDir' => 'Invalid Value Error: Allowed values: ' . implode(', ', $orderDirections)]);
            }
        }

        return $errors;
    }

    protected function validatePaginationParams(QueryParams $params): array {
        $errors = [];

        if(is_string($params->limit) && ! ctype_digit($params->limit)) {
            $errors['limit'][] = 'Invalid Type Error: limit has to be integer';
            // throw new ValidationException(['limit' => 'Invalid Type Error: limit has to be integer']);
        } else {
            $params->limit = (int) $params->limit;
        }

        if($params->limit < 1 || $params->limit > 100) {
            $errors['limit'][] = 'Invalid Value Error: limit has to be greater than 1 and less than 100';
            // throw new ValidationException(['limit' => 'Invalid Value Error: limit has to be greater than 1' . ' ' . $query->limit]);
        }

        if(is_string($params->page) && ! ctype_digit($params->page)) {
            $errors['page'][] = 'Invalid Type Error: page has to be integer';
            // throw new ValidationException(['page' => 'Invalid Type Error: page has to be integer']);
        } else {
            $params->page = (int) $params->page;
        }

        if($params->page < 1) {
            // throw new ValidationException(['page' => 'Invalid Value Error: page has to be greater than 1']);
            $errors['page'][] = 'Invalid Value Error: page has to be greater than 1';
        }

        return $errors;
    }

    protected function validateFilterParams(QueryParams $params): array {
        // TODO: do I need to validate the name parameter ? for category ?
        return [];
    }
}