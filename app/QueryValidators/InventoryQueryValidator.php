<?php
declare(strict_types=1);

namespace App\QueryValidators;

use App\DataObjects\InventoryQueryParams;
use App\DataObjects\QueryParams;

class InventoryQueryValidator extends BaseQueryValidator
{
    public function __construct(array $allowedAttributes)
    {
        parent::__construct($allowedAttributes);
    }

    protected function validateFilterParams(QueryParams $params): array
    {
        $errors = [];

        /** @var InventoryQueryParams $params */

        if($params->warehouseId !== null) {
            if (is_string($params->warehouseId) && !ctype_digit($params->warehouseId)) {
                $errors['warehouseId'][] = 'Invalid Type Error: warehouseId has to be integer';
            } else {
                $params->warehouseId = (int) $params->warehouseId;
            }
        }

        if($params->productId !== null) {
            if (is_string($params->productId) && !ctype_digit($params->productId)) {
                $errors['productId'][] = 'Invalid Type Error: productId has to be integer';
            } else {
                $params->productId = (int) $params->productId;
            }
        }

        if($params->quantity !== null) {
            if (is_string($params->quantity) && !ctype_digit($params->quantity)) {
                $errors['quantity'][] = 'Invalid Type Error: quantity has to be integer';
            } else {
                $params->quantity = (int) $params->quantity;
            }
        }

        return $errors;
    }
}