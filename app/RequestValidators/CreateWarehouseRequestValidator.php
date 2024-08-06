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
        // TODO: Implement validate() method.

        $v = new Validator($data);

        $v->rule('required', 'name');
        $v->rule('regex', 'name', '/^[A-Za-z ]*$/');

        if(array_key_exists('address_id', $data)) {
            // make sure that the given id exists

            $v->rule(function($field, $value, $params, $fields) use(&$data) {
                $address = $this->entityManager->getRepository(Address::class)->find($value); //TODO: this query needs testing

                if($address == null) {
                    return false;
                }

                $data['address_id'] = $address;
                return true;

            }, "address_id")->message("entered address does not exist");

        } else {
            $v->rule('required', ['country', 'governorate', 'district', 'street', 'building']);
        }

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}