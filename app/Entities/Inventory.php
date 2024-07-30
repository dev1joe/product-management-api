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

#[Table, Entity]
class Inventory
{
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column, ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Warehouse $warehouse;
    #[Column, ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Product $product;
    #[Column]
    private int $count;
    #[Column]
    private \DateTime $lastRestock;

    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): Inventory
    {
        $this->count = $count;
        return $this;
    }

    public function decrementCount(int $amount): Inventory
    {
        $this->count = $this->count - $amount;
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