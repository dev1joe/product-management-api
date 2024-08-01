<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'customers')]
class Customer extends Person
{
}

//TODO: on customer delete functions (in workflow project file)