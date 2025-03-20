<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Product;
use App\Entities\Warehouse;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class UpdateInventroyRequestValidator implements RequestValidatorInterface
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

        if(array_key_exists('product', $data)) {
            $v->rule('integer', 'product');

            $v->rule(function($field, $value, $params, $fields) use(&$data) {
                $product = $this->entityManager->find(Product::class, (int) $value);

                if(! $product) {
                    return false;
                }

                $data['product'] = $product;
                return true;

            }, 'product')->message('product not found');
        }

        if(array_key_exists('warehouse', $data)) {
            $v->rule('integer', 'warehouse');

            $v->rule(function($field, $value, $params, $fields) use(&$data) {
                $warehouse = $this->entityManager->find(Warehouse::class, (int) $value);

                if(! $warehouse) {
                    return false;
                }

                $data['warehouse'] = $warehouse;
                return true;

            }, 'warehouse')->message('warehouse not found');
        }


        if(array_key_exists('quantity', $data)) {
            $v->rule('integer', 'quantity');
        }

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}