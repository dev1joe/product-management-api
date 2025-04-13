<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use Valitron\Validator;

class CreateManufacturerRequestValidator implements RequestValidatorInterface
{
    public function __construct(

    ){}

    public function validate(array $data):array {
        $v = new Validator($data);

        $v->rule('required', ['name', 'email']);
        $v->rule('lengthMin', 'name', 3);
        $v->rule('regex', 'name', '/^[A-Za-z ]*$/');
        $v->rule('email', 'email');

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}