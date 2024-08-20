<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Administrator;
use App\Entities\Customer;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class RegisterCustomerRequestValidator implements RequestValidatorInterface
{
    public function __construct(private readonly EntityManager $entityManager){
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['firstName', 'lastName','email', 'password', 'confirmPassword']);

        $names = ['firstName', 'lastName'];
        if(array_key_exists('middleName', $data)) {
            $names[] = 'middleName';
        }

        $v->rule('regex', $names, '/^[A-Za-z\s]*$/');
        $v->rule('email', 'email');
        $v->rule('equals', 'confirmPassword', 'password')->label('Confirm Password');
        $v->rule(
            function($field, $value, $params, $fields) {
                $customersExist = (bool) $this->entityManager->getRepository(Customer::class)->count(['email' => $value]);
                $adminsExist = (bool) $this->entityManager->getRepository(Administrator::class)->count(['email' => $value]);

                return(! $customersExist && ! $adminsExist);
            }, 'email'
        )->message('User with the given email address already exists');

        $func = function($data) {

        };


        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}