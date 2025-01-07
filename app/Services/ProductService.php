<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\ProductQueryParams;
use App\Entities\Category;
use App\Entities\Manufacturer;
use App\Entities\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\UploadedFileInterface;

class ProductService
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

    public function fetchAll(): array {
        return $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')->select('p', 'c')->leftJoin('p.category', 'c')
            ->getQuery()->getArrayResult();
    }

    public function fetchPaginatedProducts(ProductQueryParams $params): array {
        // execute query and return result
        $query =  $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('p', 'c', 'm')
            ->leftJoin('p.category' , 'c')
            ->leftJoin('p.manufacturer', 'm');

        $this->applyFilters($query, $params);

        return $query->getQuery()->getArrayResult();
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

    private function fetchProduct(int $id): Query {
        return $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('p', 'c', 'm')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.manufacturer', 'm')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
    }

    public function fetchByIdAsArray(int $id): array {
        // this returns an array with one product inside,
        //so we just return the first product so that the response is a json object
        return $this->fetchProduct($id)->getArrayResult()[0];
    }

    public function fetchById(int $id): ?Product {
        return $this->fetchProduct($id)->getOneOrNullResult();
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
     */
    public function selectiveUpdate(Product $product, array $data): array {
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

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(Product $product): void {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}