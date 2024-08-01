<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'administrators')]
class Administrator extends Person
{
    #[Column]
    private string $ssn;

    public function getSsn(): string
    {
        return $this->ssn;
    }

    public function setSsn(string $ssn): Administrator
    {
        $this->ssn = $ssn;
        return $this;
    }

}