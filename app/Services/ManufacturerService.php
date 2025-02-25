<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Manufacturer;
use Doctrine\ORM\EntityManager;

class ManufacturerService extends BaseService
{
    public function __construct(
        public readonly EntityManager $entityManager,
    ){
        parent::__construct(
            $this->entityManager,
            Manufacturer::class
        );
    }

    public function fetchIdsNames(): array {
        return $this->entityManager->getRepository(Manufacturer::class)
            ->createQueryBuilder('m')
            ->select('m.id', 'm.name')
            ->getQuery()->getArrayResult();
    }
}