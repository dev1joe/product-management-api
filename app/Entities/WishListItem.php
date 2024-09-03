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

#[Entity, Table(name: 'wishlist_items')]
class WishListItem
{
    #[Id, Column, GeneratedValue]
    private int $id;
    #[ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Product $product;
    #[ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Customer $customer;

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): WishListItem
    {
        $this->product = $product;
        return $this;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): WishListItem
    {
        $this->customer = $customer;
        return $this;
    }
}