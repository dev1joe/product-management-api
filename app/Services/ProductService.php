<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\ProductQueryParams;
use App\Entities\Category;
use App\Entities\Manufacturer;
use App\Entities\Product;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Exception;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\UploadedFileInterface;

class ProductService extends BaseService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly FileService $fileService,
    ){
        parent::__construct(
            $this->entityManager,
            Product::class
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws FilesystemException
     * @throws ORMException
     */
    public function create(array $data): Product {
        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);

        // price handling
        $price = (float) $data['price'];
        // converting from dollars to cents
        $price *= 100;
        $product->setUnitPriceInCents((int) $price);

        if(array_key_exists('manufacturer', $data)) {
            $product->setManufacturer($data['manufacturer']);
        }

        // category handling
        /** @var Category $category */
        $category = $data['category'];
        // $category->incrementProductCount(1);
        // $this->entityManager->persist($category);
        $product->setCategory($category);

        // photo handling
        /** @var UploadedFileInterface $file */
        $file = $data['photo'];
        $relativeLocation = $this->fileService->saveProductImage($file);

        $product->setPhoto($relativeLocation);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function queryAll(): QueryBuilder
    {
        return $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('r')
            ->select('r', 'c', 'm')
            ->leftJoin('r.category', 'c')
            ->leftJoin('r.manufacturer', 'm');
    }

    public function fetchPaginationMetadata(?ProductQueryParams $params): array {
        // execute query and return result
        $query =  $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('COUNT(p.id) AS count, MIN(p.unitPriceInCents) AS minPrice, MAX(p.unitPriceInCents) AS maxPrice');

        if($params) {
            $query->leftJoin('p.category' , 'c')->leftJoin('p.manufacturer', 'm');
            $this->applyFilters($query, $params);
            return $query->getQuery()->getArrayResult();
        }

        return $query->getQuery()->getArrayResult();
    }

    private function applyFilters(QueryBuilder $query, ProductQueryParams $params): void {
        if($params->categoryId) {
            $query->where('c.id = :id')->setParameter('id', $params->categoryId);
        }

        if($params->minPriceInCents) {
            $query->andWhere('p.unitPriceInCents > :min')->setParameter('min', $params->minPriceInCents);
        }

        if($params->maxPriceInCents) {
            $query->andWhere('p.unitPriceInCents < :max')->setParameter('max', $params->maxPriceInCents);
        }

        // calculate offset
        $offset = ($params->page - 1) * $params->limit;

        $query->orderBy("p.".$params->orderBy, $params->orderDir)
            ->setFirstResult($offset)
            ->setMaxResults($params->limit);
    }

    public function fetchByCategory(int $id): array {
        return $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('p', 'c', 'm')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.manufacturer', 'm')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array{bool, string}
     * @throws OptimisticLockException
     * @throws FilesystemException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function selectiveUpdate(int $id, array $data): array {
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        if(! $product) {
            throw new EntityNotFoundException('Product Not Found');
        }

        $changedFields = [];

        // assuming that the data went through the request validator first
        /** @var Category $newCategory */
        $newCategory = $data['category'];
        $currentCategory = $product->getCategory();

        if(! $currentCategory || ($currentCategory->getId() !== $newCategory->getId())) {
            $product->setCategory($newCategory);
            $changedFields[] = 'category';
        }

        /** @var Manufacturer $newManufacturer */
        $newManufacturer = $data['manufacturer'];
        $currentManufacturer = $product->getManufacturer();

        if(! $currentManufacturer || ($currentManufacturer->getId() !== $newManufacturer->getId())) {
            $product->setManufacturer($newManufacturer);
            $changedFields[] = 'manufacturer';
        }

        if(array_key_exists('photo', $data)) {
            /** @var UploadedFileInterface $file */
            $file = $data['photo'];
            $relativeLocation = $this->fileService->saveProductImage($file);

            $product->setPhoto($relativeLocation);
            $changedFields[] = 'photo';
        }

        // assuming that data went through the request validator first
        $price = (float) $data['price'];
        $price *= 100; // from dollars to cents
        $priceInCents = (int) $price;
        if($product->getUnitPriceInCents() !== $priceInCents) {
            $product->setUnitPriceInCents($priceInCents);
            $changedFields[] = 'price';
        }

        if($product->getName() !== $data['name']) {
            $product->setName($data['name']);
            $changedFields[] = 'name';
        }

        if($product->getDescription() !== $data['description']) {
            $product->setDescription($data['description']);
            $changedFields[] = 'description';
        }

        if(sizeof($changedFields) > 0) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }

        return $changedFields;
    }
}