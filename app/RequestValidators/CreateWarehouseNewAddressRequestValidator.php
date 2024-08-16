<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use Valitron\Validator;

class CreateWarehouseNewAddressRequestValidator implements RequestValidatorInterface
{

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', [
            'name', 'country', 'governorate',
            'district', 'street', 'building'
        ]);

        $v->rule('regex', 'name', '/^[A-Za-z ]*$/');

        //TODO: more validation

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}