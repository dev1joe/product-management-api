<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\DataObjects\AddressQueryParams;
use App\DataObjects\QueryParams;

class AddressQueryValidator extends BaseQueryValidator
{
    public function __construct(array $allowedAttributes)
    {
        parent::__construct($allowedAttributes);
    }

    protected function validateFilterParams(QueryParams $params): array
    {
        $errors = [];

        /** @var AddressQueryParams $params */

        return $errors;
        // used to cancel "name" parameter validation
    }
}