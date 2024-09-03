<?php
declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\HasSoftDelete;
use App\Entities\Traits\HasTimestamps;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'warehouses')]
#[HasLifecycleCallbacks]
class Warehouse
{
    use HasTimestamps;
    use HasSoftDelete;
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column]
    private string $name;
    #[ManyToOne(targetEntity: Address::class)]
    #[JoinColumn(name: 'address_id', referencedColumnName: 'id', onDelete: 'RESTRICT')]
    private Address $address;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Warehouse
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Warehouse
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): Warehouse
    {
        $this->address = $address;
        return $this;
    }

}