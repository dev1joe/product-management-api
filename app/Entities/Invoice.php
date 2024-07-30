<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table]
class Invoice
{
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column, OneToOne(targetEntity: Order::class, mappedBy: 'invoice')]
    private Order $order;
    #[Column]
    private float $tax;
    #[Column]
    private string $status; // TODO: refactor to enum
    #[Column(name: 'billing_details')]
    private string $billingDetails;
    #[Column]
    private float $total;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Invoice
    {
        $this->id = $id;
        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): Invoice
    {
        $this->order = $order;
        return $this;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): Invoice
    {
        $this->tax = $tax;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Invoice
    {
        $this->status = $status;
        return $this;
    }

    public function getBillingDetails(): string
    {
        return $this->billingDetails;
    }

    public function setBillingDetails(string $billingDetails): Invoice
    {
        $this->billingDetails = $billingDetails;
        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): Invoice
    {
        $this->total = $total;
        return $this;
    }

}