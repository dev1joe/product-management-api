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
        // TODO: Example: if the client enters a number as the country name,
        //  the query will return an empty array, and the response will be 204,
        //  then why validate parameters?

        return $errors;
        // used to cancel "name" parameter validation
    }
}