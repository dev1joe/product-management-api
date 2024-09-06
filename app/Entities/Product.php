<?php
declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\HasSoftDelete;
use App\Entities\Traits\HasTimestamps;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'products')]
#[HasLifecycleCallbacks]
class Product
{
    use HasTimestamps;
    use HasSoftDelete;
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column]
    private string $name;
    #[Column(type: Types::STRING, length: 1000)]
    private string $description;
    #[ManyToOne, JoinColumn(name: 'category_id', onDelete: 'RESTRICT')]
    private ?Category $category;
    #[ManyToOne, JoinColumn(name: 'manufacturer_id', onDelete: 'RESTRICT')]
    private ?Manufacturer $manufacturer;
    #[Column]
    private string $photo;
    #[Column(name: "unit_price_in_cents")]
    private int $unitPriceInCents;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): Product
    {
        $this->category = $category;
        return $this;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer): Product
    {
        $this->manufacturer = $manufacturer;
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

    public function getUnitPriceInCents(): int
    {
        return $this->unitPriceInCents;
    }

    public function setUnitPriceInCents(int $unitPriceInCents): Product
    {
        $this->unitPriceInCents = $unitPriceInCents;
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