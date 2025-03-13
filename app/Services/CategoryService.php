<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\UploadedFileInterface;

class CategoryService extends BaseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly FileService $fileService,
    ){
        parent::__construct(
            $this->entityManager,
            Category::class
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws FilesystemException
     * @throws ORMException
     */
    public function create(array $data): Category {
        $category = new Category();
        $category->setName($data['name']);
        $category->setProductCount(0);

        if(isset($data['image'])) {
            /** @var UploadedFileInterface $file */
            $file = $data['image'];
            $relativePath = $this->fileService->saveCategoryImage($file);

             $category->setImage($relativePath);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

//    public function fetchIdsNames(): array {
//        return $this->entityManager->getRepository(Category::class)->createQueryBuilder('c')
//            ->select('c.id', 'c.name')->getQuery()->getArrayResult();
//    }

    /**
     * @throws FilesystemException
     * @throws ORMException
     */
    public function update(int $id, array $data): Category {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        if(! $category) {
            throw new EntityNotFoundException('Category Not Found');
        }

        if(isset($data['name'])) {
            $category->setName($data['name']);
        }

        if(isset($data['image'])) {
            /** @var UploadedFileInterface $file */
            $file = $data['image'];

            $relativePath = $this->fileService->saveCategoryImage($file);
            $category->setImage($relativePath);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

//    /**
//     * @throws OptimisticLockException
//     * @throws ORMException
//     */
//    public function delete(Category $category): void {
//        $this->entityManager->remove($category);
//        $this->entityManager->flush();
//    }

    //TODO: recalculate connections
    // count how many products associated with each category and check if category->productCount is wrong or not
    // if wrong change it
}