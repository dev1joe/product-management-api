<?php
declare(strict_types=1);

namespace App\Services;

use App\DataObjects\ProductQueryParams;
use App\DataObjects\QueryParams;
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
        if(isset($data['photo'])) {
            /** @var UploadedFileInterface $file */
            $file = $data['photo'];
            $relativeLocation = $this->fileService->saveProductImage($file);

            $product->setPhoto($relativeLocation);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function queryAll(?QueryParams $params = null): QueryBuilder
    {
        $query = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('r')
            ->select('r', 'c', 'm')
            ->leftJoin('r.category', 'c')
            ->leftJoin('r.manufacturer', 'm');

        if($params) {
            return $this->applyFilters($query, $params);
        }

        return $query;
    }

    protected function applyFilters(QueryBuilder $query, QueryParams $params): QueryBuilder {
        /** @var ProductQueryParams $params */

        if($params->name) {
            $query->where('r.name LIKE :name')->setParameter('name', $params->name);
        }

        if($params->categoryId) {
            $query->andWhere('c.id = ?0')->setParameter(0, $params->categoryId);
        }

        if($params->manufacturerId) {
            $query->andWhere('m.id = ?1')->setParameter(1, $params->manufacturerId);
        }

        if($params->minPriceInCents) {
            $query->andWhere('r.unitPriceInCents > :min')->setParameter('min', $params->minPriceInCents);
        }

        if($params->maxPriceInCents) {
            $query->andWhere('r.unitPriceInCents < :max')->setParameter('max', $params->maxPriceInCents);
        }

        if($params->rating) {
            $query->andWhere('r.avgRating = :rating')->setParameter('rating', $params->rating);
        }

        return $query;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws FilesystemException
     * @throws EntityNotFoundException
     */
    public function update(int $id, array $data): Product {
        $product = $this->entityManager->find(Product::class, $id);
        if(! $product) {
            throw new EntityNotFoundException("Product Not Found");
        }

        if(array_key_exists('name', $data)) {
            $product->setName($data['name']);
        }

        if(array_key_exists('category', $data)) {
            $product->setCategory($data['category']);
        }

        if(array_key_exists('manufacturer', $data)) {
            $product->setManufacturer($data['manufacturer']);
        }

        if(array_key_exists('description', $data)) {
            $product->setDescription($data['description']);
        }

        if(array_key_exists('price', $data)) {
            $product->setUnitPriceInCents((int) $data['price']);
        }

        // photo handling
        if(isset($data['photo'])) {
            /** @var UploadedFileInterface $file */
            $file = $data['photo'];
            $relativeLocation = $this->fileService->saveProductImage($file);

            $product->setPhoto($relativeLocation);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
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