<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Address;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class CreateWarehouseRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['name', 'address']);
        $v->rule('regex', 'name', '/^[A-Za-z ]*$/');
        $v->rule('integer', 'address');

        $v->rule(function($field, $value, $params, $fields) use(&$data) {
            if(! array_key_exists('address', $data)) {
                return false;
            }

            $address = $this->entityManager->getRepository(Address::class)->find($value);

            if($address == null) {
                return false;
            }

            $data['address'] = $address;
            return true;

        }, "address")->message("address not found");

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}