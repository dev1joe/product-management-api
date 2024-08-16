<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
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


        if($data['address_type'] == 'existing') {


        } else if($data['address_type'] == 'new') {


        }


    }
}