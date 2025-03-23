<?php
declare(strict_types=1);

namespace App\DataObjects;


class InventoryQueryParams extends QueryParams
{
    public mixed $warehouseId = null;
    public mixed $productId = null;
    public mixed $quantity = null;

    public function __construct(array $params) {
        parent::__construct($params);

        $this->warehouseId = (isset($params['warehouse']))? $params['warehouse'] : null;

        $this->productId = (isset($params['product']))? $params['product'] : null;

        $this->quantity = (isset($params['quantity']))? $params['quantity'] : null;
    }
}