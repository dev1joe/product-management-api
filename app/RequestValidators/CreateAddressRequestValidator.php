<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use Valitron\Validator;

class CreateAddressRequestValidator implements RequestValidatorInterface
{

    public function validate(array $data): array
    {
        $features = ['country', 'governorate', 'district'];

        $v = new Validator($data);

        $v->rule('required', ['country', 'governorate', 'district']);
        $v->rule('regex', ['country', 'governorate', 'district'], '/^[A-Za-z\s-]{3,50}$/');

        if(isset($data['street'])) {
            $v->rule('regex', 'street', '/^[A-Za-z\s-]{3,50}$/');
        }

        if(isset($data['building'])) {
            $v->rule('regex', 'building', '/^[A-Za-z\s-]{3,50}$/');
        }

        if(isset($data['floor'])) {
            $v->rule('integer', 'floor');
        }

        if(isset($data['apartment'])) {
            $v->rule('integer', 'apartment');
        }

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}