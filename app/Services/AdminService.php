<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Administrator;
use Doctrine\ORM\EntityManager;

class AdminService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function fetchByEmail(string $email): Administrator|null {
        return $this->entityManager->getRepository(Administrator::class)->findOneBy(['email' => $email]);
    }
}