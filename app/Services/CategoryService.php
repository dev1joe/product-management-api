<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\UploadedFileInterface;

class CategoryService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly FileService $fileService,
    ){
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

    public function fetchAll(): array{
        return $this->entityManager->getRepository(Category::class)
            ->createQueryBuilder('c')->select('c')->getQuery()->getArrayResult();
    }

    /**
     * assumes that query parameters exit and validated
     * @param array $queryParams
     * @return array
     */
    public function fetchPaginatedCategories(array $queryParams): array {
        return $this->entityManager->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.' . $queryParams['orderBy'], $queryParams['orderDir'])
            ->getQuery()
            ->getArrayResult();
    }

    public function fetchCategoryNames(): array {
        return $this->entityManager->getRepository(Category::class)->createQueryBuilder('c')
            ->select('c.id', 'c.name')->getQuery()->getArrayResult();
    }

    private function fetchCategory(int $id): Query {
        return $this->entityManager
            ->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->select('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
    }

    /**
     * @throws ORMException
     */
    public function fetchById(int $id): ?Category {
        return $this->fetchCategory($id)->getOneOrNullResult();
    }

    public function fetchByIdAsArray(int $id): array {
        return $this->fetchCategory($id)->getArrayResult();
    }

    /**
     * @throws FilesystemException
     * @throws ORMException
     */
    public function update(Category $category, array $data): Category {
        $category->setName($data['name']);

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

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(Category $category) {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    //TODO: recalculate connections
    // count how many products associated with each category and check if category->productCount is wrong or not
    // if wrong change it
}