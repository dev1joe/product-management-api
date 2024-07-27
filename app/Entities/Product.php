<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Table, Entity]
class Product
{
    #[Id, Column]
    private int $id;
    #[Column]
    private string $name;
    #[Column]
    private string $description;
    #[Column] // TODO: tutorial about orm relationships
    private Category $category;
    #[Column] // TODO: it should be the path of the photo
    private string $photo;
    #[Column(name: "unit_price")]
    private float $unitPrice;
    #[Column(name: "avg_rating")]
    private float $avgRating;

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

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): Product
    {
        $this->unitPrice = $unitPrice;
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