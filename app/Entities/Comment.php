<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Table, Entity]
class Comment
{// TODO: Id ??
    #[Column]
    private Customer $customer;
    #[Column]
    private Product $product;
    #[Column]
    private \DateTime $timestamp;
    #[Column]
    private string $content;
    #[Column]
    private float $rating;

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): Comment
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Comment
    {
        $this->content = $content;
        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): Comment
    {
        $this->rating = $rating;
        return $this;
    }

}