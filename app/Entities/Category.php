<?php
declare(strict_types=1);

namespace App\Entities;
use App\Entities\Traits\HasTimestamps;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'categories')]
#[HasLifecycleCallbacks]
class Category
{
    use HasTimestamps;
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column]
    private string $name;
    #[Column(name: 'product_count', options: ['default' => 0])]
    private int $productCount = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Category
    {
        $this->name = $name;
        return $this;
    }

    public function getProductCount(): int
    {
        return $this->productCount;
    }

    public function setProductCount(int $productCount): Category
    {
        $this->productCount = $productCount;
        return $this;
    }

    public function incrementProductCount(int $amount): Category
    {
        $this->productCount = $this->productCount + $amount;
        return $this;
    }

    public function decrementProductCount(int $amount): Category
    {
        $this->productCount = $this->productCount - $amount;
        return $this;
    }
}