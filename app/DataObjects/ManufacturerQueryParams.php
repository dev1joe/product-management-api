<?php
declare(strict_types=1);

namespace App\DataObjects;

class ManufacturerQueryParams extends QueryParams
{
    public ?string $email = null;

    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->email = (isset($params['email']))? $params['email'] : null;
    }
}