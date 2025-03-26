<?php
declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\HasTimestamps;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'addresses')]
class Address
{
    use HasTimestamps;
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column]
    private string $country;
    #[Column]
    private string $governorate;
    #[Column]
    private string $district;
    #[Column(nullable: true)]
    private ?string $street;
    #[Column(nullable: true)]
    private ?string $building;
    #[Column(nullable: true)]
    private ?int $floor;
    #[Column(nullable: true)]
    private ?int $apartment;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Address
    {
        $this->id = $id;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): Address
    {
        $this->country = $country;
        return $this;
    }

    public function getGovernorate(): string
    {
        return $this->governorate;
    }

    public function setGovernorate(string $governorate): Address
    {
        $this->governorate = $governorate;
        return $this;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function setDistrict(string $district): Address
    {
        $this->district = $district;
        return $this;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): Address
    {
        $this->street = $street;
        return $this;
    }

    public function getBuilding(): string
    {
        return $this->building;
    }

    public function setBuilding(string $building): Address
    {
        $this->building = $building;
        return $this;
    }

    public function getFloor(): int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): Address
    {
        $this->floor = $floor;
        return $this;
    }

    public function getApartment(): int
    {
        return $this->apartment;
    }

    public function setApartment(int $apartment): Address
    {
        $this->apartment = $apartment;
        return $this;
    }

}