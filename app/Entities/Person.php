<?php
declare(strict_types=1);

namespace App\Entities;

use App\Contracts\AuthenticatableInterface;
use App\Entities\Traits\HasSoftDelete;
use App\Entities\Traits\HasTimestamps;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\OneToOne;

#[MappedSuperclass]
#[HasLifecycleCallbacks]
class Person implements AuthenticatableInterface
{
    use HasTimestamps;
    use HasSoftDelete;
    #[Id, Column, GeneratedValue]
    private int $id;
    #[Column(name: 'first_name')]
    private string $firstName;
    #[Column(name: 'middle_name')]
    private string $middleName;
    #[Column(name: 'last_name')]
    private string $lastName;
    #[Column(unique: true)]
    private string $email;
    #[Column]
    private string $password;
    #[OneToOne, JoinColumn(name: 'address_id', nullable: true, onDelete: 'RESTRICT')]
    private Address $address;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): Person
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function setMiddleName(string $middleName): Person
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): Person
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Person
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): Person
    {
        $this->password = $password;
        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): Person
    {
        $this->address = $address;
        return $this;
    }

    public function getUsername(): string {
        $username = $this->firstName . ' ' . $this->lastName;
        return $username;
    }
}