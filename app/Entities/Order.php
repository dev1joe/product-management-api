<?php
declare(strict_types=1);

namespace App\Entities;

use App\Enums\OrderStatus;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'orders')]
class Order
{
    #[Id, Column, GeneratedValue]
    private int $id;

    #[ManyToOne]
    private Customer $customer;
    #[OneToOne]
    private Receipt $receipt;
    #[Column(name: 'date_created')]
    private \DateTime $dateCreated;
    #[Column(name: 'date_delivered')]
    private \DateTime $dateDelivered;
    #[Column(type: Types::STRING)]
    private OrderStatus $status = OrderStatus::Pending;
    #[Column(name: 'total_cents')]
    private int $totalCents;

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

    public function getTotalCents(): float
    {
        return $this->totalCents;
    }

    public function setTotalCents(int $totalCents): Order
    {
        $this->totalCents = $totalCents;
        return $this;
    }

    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(Receipt $receipt): Order
    {
        $this->receipt = $receipt;
        return $this;
    }
}