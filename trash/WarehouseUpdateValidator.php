<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Address;
use App\Entities\Warehouse;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

/**
 * detects whether the user changed warehouse data or not
 * prevents unnecessary reads and writes
 */
class WarehouseUpdateValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function validate(array $data): array
    {

        $v = new Validator($data);

        $v->rule('required', 'id');

        $v->rule(function($field, $value, $params, $fields) use(&$data) {
            /** @var Warehouse $oldWarehouse */
            $oldWarehouse = $this->entityManager->find(Warehouse::class, (int) $data['id']);

            /** @var Address $newAddress */
            $newAddress = $data['address_id'];

            if(
                array_key_exists('address_id', $data) &&
                ($oldWarehouse->getName() == $data['name']) &&
                (($oldWarehouse->getAddress())->getId() == $newAddress->getId())
            ) {
                return false;
            } else {
                return true;
            }
        }, ['name', 'address_id'])->message('Please update the data');

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}