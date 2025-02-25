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

#[Entity, Table(name: 'manufacturers')]
#[HasLifecycleCallbacks]
class Manufacturer
{
    use HasTimestamps; // TODO: soft delete ?
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column]
    private string $name;
    #[Column]
    private string $email;
    #[Column]
    private string $logo;
    #[Column(name: 'product_count')]
    private int $productCount;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Manufacturer
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Manufacturer
    {
        $this->email = $email;
        return $this;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): Manufacturer
    {
        $this->logo = $logo;
        return $this;
    }

    public function getProductCount(): int
    {
        return $this->productCount;
    }

    public function setProductCount(int $productCount): Manufacturer
    {
        $this->productCount = $productCount;
        return $this;
    }

    public function incrementProductCount(int $amount): Manufacturer {
        $this->productCount += $amount;
        return $this;
    }

    public function decrementProductCount(int $amount): Manufacturer {
        $this->productCount -= $amount;
        return $this;
    }
}