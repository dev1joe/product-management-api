<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Product;
use App\Entities\Warehouse;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class CreateInventoryRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager
    ){
    }

    /**
     * @inheritDoc
     */
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['warehouse', 'product', 'quantity']);
        $v->rule('integer', ['warehouse', 'product', 'quantity']);

        $v->rule(function($field, $value, $params, $fields) use(&$data) {
            $warehouse = $this->entityManager->find(Warehouse::class, (int) $value);

            if(! $warehouse) {
                return false;
            }

            $data['warehouse'] = $warehouse;
            return true;

        }, 'warehouse')->message('warehouse not found');

        $v->rule(function($field, $value, $params, $fields) use(&$data) {
            $product = $this->entityManager->find(Product::class, (int) $value);

            if(! $product) {
                return false;
            }

            $data['product'] = $product;
            return true;

        }, 'product')->message('product not found');

        // TODO: if (isset($data['lastrestock'])) validate it's a date time object

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}