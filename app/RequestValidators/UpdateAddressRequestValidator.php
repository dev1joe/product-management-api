<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use Valitron\Validator;

class UpdateAddressRequestValidator implements RequestValidatorInterface
{

    /**
     * @inheritDoc
     */
    public function validate(array $data): array
    {
        $v = new Validator($data);

        if(array_key_exists('country', $data)) {
            $v->rule('regex', 'country', '/^[A-Za-z\s-]*$/');
            $v->rule('lengthBetween', 'country', 3, 50);
            // for suitable error message use valitron to validate length instead of regex
        }

        if(array_key_exists('governorate', $data)) {
            $v->rule('regex', 'governorate', '/^[A-Za-z\s-]*$/');
            $v->rule('lengthBetween', 'governorate', 3, 50);
        }

        if(array_key_exists('district', $data)) {
            $v->rule('regex', 'district', '/^[A-Za-z\s-]*$/');
            $v->rule('lengthBetween', 'district', 3, 50);
        }

        if(array_key_exists('street', $data)) {
            $v->rule('regex', 'street', '/^[A-Za-z\s-]*$/');
            $v->rule('lengthBetween', 'street', 3, 50);
        }

        if(array_key_exists('building', $data)) {
            $v->rule('regex', 'building', '/^[A-Za-z\s-]*$/');
            $v->rule('lengthBetween', 'building', 3, 50);
        }

        if(array_key_exists('floor', $data)) {
            $v->rule('integer', 'floor');
        }

        if(array_key_exists('apartment', $data)) {
            $v->rule('integer', 'apartment');
        }

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}