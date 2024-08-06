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

#[Entity, Table(name: 'order_items')]
class OrderItem
{
    #[Id, Column, GeneratedValue]
    private int $id;
    #[ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Customer $customer;
    #[ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Product $product;
    #[Column]
    private int $quantity;
    #[Column(name: 'total_price_cents')]
    private int $totalPriceCents;

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): OrderItem
    {
        $this->customer = $customer;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): OrderItem
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): OrderItem
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getTotalPriceCents(): float
    {
        return $this->totalPriceCents;
    }

    public function setTotalPriceCents(int $totalPriceCents): OrderItem
    {
        $this->totalPriceCents = $totalPriceCents;
        return $this;
    }

}