<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\DataObjects\ManufacturerQueryParams;
use App\DataObjects\QueryParams;

class ManufacturerQueryValidator extends BaseQueryValidator
{
    public function __construct(array $allowedAttributes)
    {
        parent::__construct($allowedAttributes);
    }

    protected function validateFilterParams(QueryParams $params): array
    {
        $errors = [];

        /** @var ManufacturerQueryParams $params */

        if($params->email !== null && ! filter_var($params->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Invalid Value Error: invalid email address';
        }

        return $errors;
    }
}