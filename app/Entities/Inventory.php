<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'inventories')]
class Inventory
{
    #[Id, Column, GeneratedValue]
    private int $id;
    #[ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Warehouse $warehouse;
    #[ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Product $product;
    #[Column]
    private int $quantity;
    #[Column(name: 'last_restock')]
    private \DateTime $lastRestock;

    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(Warehouse $warehouse): Inventory
    {
        $this->warehouse = $warehouse;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): Inventory
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): Inventory
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function decrementQuantity(int $amount = 1): Inventory
    {
        $this->quantity = $this->quantity - $amount;
        return $this;
    }

    public function getLastRestock(): \DateTime
    {
        return $this->lastRestock;
    }

    public function setLastRestock(\DateTime $lastRestock): Inventory
    {
        $this->lastRestock = $lastRestock;
        return  $this;
    }
}