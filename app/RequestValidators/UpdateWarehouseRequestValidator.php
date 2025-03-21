<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Address;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class UpdateWarehouseRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    /**
     * @inheritDoc
     */
    public function validate(array $data): array
    {
        $v = new Validator($data);

        if(sizeof($data) == 0) throw new ValidationException(['Request Body is Empty']);

        if(array_key_exists('name', $data)) {
            $v->rule('regex', 'name', '/^[A-Za-z._,:\s\-]*$/');
        }

        if(array_key_exists('address', $data)) {
            $v->rule('integer', 'address');
        }

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