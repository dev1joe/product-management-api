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
#[HasLifecycleCallbacks] // allow updating timestamps using lifecycle callbacks
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
    #[Column(name: "price_in_cents")]
    private int $priceInCents;
    #[Column(type: Types::DECIMAL, precision: 2, scale: 1, options: ["default" => 0])]
    private float $rating = 0;

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

    public function getPriceInCents(): int
    {
        return $this->priceInCents;
    }

    public function setPriceInCents(int $priceInCents): Product
    {
        $this->priceInCents = $priceInCents;
        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): Product
    {
        $this->rating = $rating;
        return $this;
    }

}