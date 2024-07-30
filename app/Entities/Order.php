<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Table, Entity]
class Order
{
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column]
    #[ManyToOne]
    private Customer $customer;
    #[Column]
    #[OneToOne(targetEntity: Invoice::class, inversedBy: 'order')]
    private Invoice $invoice;
    #[Column]
    private \DateTime $dateCreated;
    #[Column]
    private \DateTime $dateDelivered;
    #[Column]
    private string $status;
    #[Column]
    private float $total;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Order
    {
        $this->id = $id;
        return $this;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): Order
    {
        $this->customer = $customer;
        return $this;
    }

    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTime $dateCreated): Order
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateDelivered(): \DateTime
    {
        return $this->dateDelivered;
    }

    public function setDateDelivered(\DateTime $dateDelivered): Order
    {
        $this->dateDelivered = $dateDelivered;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Order
    {
        $this->status = $status;
        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): Order
    {
        $this->total = $total;
        return $this;
    }

}