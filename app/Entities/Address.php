<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Table, Entity]
class Address
{
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column, OneToOne(mappedBy: 'address'), JoinColumn(onDelete: 'CASCADE')]
    private Person $person;
    #[Column]
    private string $country;
    #[Column]
    private string $province;
    #[Column]
    private string $district;
    #[Column]
    private string $street;
    #[Column]
    private string $building;
    #[Column]
    private int $floor;
    #[Column]
    private int $apartment;

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

    public function getProvince(): string
    {
        return $this->province;
    }

    public function setProvince(string $province): Address
    {
        $this->province = $province;
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