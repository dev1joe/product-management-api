<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'products')]
class Product
{
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column]
    private string $name;
    #[Column(type: Types::STRING, length: 1000)]
    private string $description;
    #[ManyToOne]
    private Category $category;
    #[Column] // TODO: it should be the path of the photo
    private string $photo;
    #[Column(name: "unit_price_cents")]
    private int $unitPriceCents;
    #[Column(name: "avg_rating", type: Types::DECIMAL, precision: 2, scale: 1, options: ["default" => 0])]
    private float $avgRating = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Product
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): Product
    {
        $this->category = $category;
        return $this;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): Product
    {
        $this->photo = $photo;
        return $this;
    }

    public function getUnitPriceCents(): float
    {
        return $this->unitPriceCents;
    }

    public function setUnitPriceCents(int $unitPriceCents): Product
    {
        $this->unitPriceCents = $unitPriceCents;
        return $this;
    }

    public function getAvgRating(): float
    {
        return $this->avgRating;
    }

    public function setAvgRating(float $avgRating): Product
    {
        $this->avgRating = $avgRating;
        return $this;
    }

}