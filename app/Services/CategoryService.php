<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class CategoryService extends BaseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
        parent::__construct(
            $this->entityManager,
            Category::class
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(array $data): Category {
        $category = new Category();
        $category->setName($data['name']);
        $category->setProductCount(0);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

//    public function fetchIdsNames(): array {
//        return $this->entityManager->getRepository(Category::class)->createQueryBuilder('c')
//            ->select('c.id', 'c.name')->getQuery()->getArrayResult();
//    }

    /**
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function update(int $id, array $data): Category {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        if(! $category) {
            throw new EntityNotFoundException('Category Not Found');
        }

        if(isset($data['name'])) {
            $category->setName($data['name']);
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

}