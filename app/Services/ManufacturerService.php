<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Manufacturer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\UploadedFileInterface;

class ManufacturerService extends BaseService
{
    public function __construct(
        public readonly EntityManager $entityManager,
        private readonly FileService $fileService,
    ){
        parent::__construct(
            $this->entityManager,
            Manufacturer::class
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws FilesystemException
     */
    public function update(int $id, array $data): Manufacturer {
        $manufacturer = $this->entityManager->getRepository(Manufacturer::class)->find($id);

        if(! $manufacturer) {
            throw new EntityNotFoundException('Manufacturer Not Found');
        }

        if(isset($data['name'])) {
            $manufacturer->setName($data['name']);
        }

        if(isset($data['email'])) {
            $manufacturer->setEmail($data['email']);
        }

        if(isset($data['logo'])) {
            /** @var UploadedFileInterface $file */
            $file = $data['logo'];

            $relativePath = $this->fileService->saveManufacturerLogo($file);
            $manufacturer->setLogo($relativePath);
        }

        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();

        return $manufacturer;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws FilesystemException
     */
    public function create(array $data): Manufacturer {
        $manufacturer = new Manufacturer();
        $manufacturer->setName($data['name']);
        $manufacturer->setEmail($data['email']);
        $manufacturer->setProductCount(0);

        if(isset($data['logo'])) {
            /** @var UploadedFileInterface $file */
            $file = $data['logo'];
            $relativePath = $this->fileService->saveManufacturerLogo($file);

            $manufacturer->setLogo($relativePath);
        }


        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();

        return $manufacturer;
    }
}