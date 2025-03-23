<?php
declare(strict_types=1);

namespace App\DataObjects;

use App\DataObjects\QueryParams;

class AddressQueryParams extends QueryParams
{
    public ?string $country;
    public ?string $governorate;
    public ?string $district;
    public ?string $street;
    public ?string $building;

    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->country = (isset($params['country']))? $params['country'] : null;
        $this->governorate = (isset($params['governorate']))? $params['governorate'] : null;
        $this->district = (isset($params['district']))? $params['district'] : null;
        $this->street = (isset($params['street']))? $params['street'] : null;
        $this->building = (isset($params['building']))? $params['building'] : null;

    }
}