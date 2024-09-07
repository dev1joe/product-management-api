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
        // calculate offset
        $offset = ($params->page - 1) * $params->limit;

        // execute query and return result
        $query =  $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('p', 'c', 'm')
            ->leftJoin('p.category' , 'c')
            ->leftJoin('p.manufacturer', 'm');

        if($params->categoryId) {
            $query->where('c.id = :id')->setParameter('id', $params->categoryId);
        }

        if($params->minPriceInCents) {
            $query->andWhere('p.unitPriceInCents > :min')->setParameter('min', $params->minPriceInCents);
        }

        if($params->maxPriceInCents) {
            $query->andWhere('p.unitPriceInCents < :max')->setParameter('max', $params->maxPriceInCents);
        }

        $query->setFirstResult($offset)
            ->setMaxResults($params->limit)
            ->orderBy("p.".$params->orderBy, $params->orderDir);

        return $query->getQuery()->getArrayResult();
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

    public function fetchProductByIdAsArray(int $id): array {
        return $this->fetchProduct($id)->getArrayResult();
    }

    public function fetchProductById(int $id): ?Product {
        return $this->fetchProduct($id)->getOneOrNullResult();
    }

    /**
     * @return array{bool, string}
     * @throws OptimisticLockException
     * @throws FilesystemException
     * @throws ORMException
     */
    public function update(int $id, array $data): array {
        $product = $this->fetchProductById($id);
        $isChanged = false;
        $message = '';

        // assuming that the data went through the request validator first
        /** @var Category $newCategory */
        $newCategory = $data['category'];
        $currentCategory = $product->getCategory();

        if(! $currentCategory) {
            $isChanged = true;
            $message = $message . 'category changed ';
            $product->setCategory($data['category']);
        } else if($currentCategory->getId() !== $newCategory->getId()) {
            $isChanged = true;
            $message = $message . 'category changed ';
            $product->setCategory($newCategory);
        }

        /** @var Manufacturer $newManufacturer */
        $newManufacturer = $data['manufacturer'];
        $currentManufacturer = $product->getManufacturer();

        if(! $currentManufacturer) {
            $isChanged = true;
            $message = $message . 'manufacturer changed ';
            $product->setManufacturer($newManufacturer);
        } else if($currentManufacturer->getId() !== $newManufacturer->getId()) {
            $isChanged = true;
            $message = $message . 'manufacturer changed ';
            $product->setManufacturer($newManufacturer);
        }

        if(array_key_exists('photo', $data)) {
            $isChanged = true;
            $message = $message . 'photo changed ';

            /** @var UploadedFileInterface $file */
            $file = $data['photo'];
            $relativeLocation = $this->fileService->saveProductImage($file);

            $product->setPhoto($relativeLocation);
        }

        // assuming that data went through the request validator first
        $price = (float) $data['price'];
        $price *= 100; // from dollars to cents
        $priceInCents = (int) $price;
        if($product->getUnitPriceInCents() !== $priceInCents) {
            $isChanged = true;
            $message = 'price changed ';

            $product->setUnitPriceInCents($priceInCents);
        }

        if($product->getName() !== $data['name']) {
            $isChanged = true;
            $message = $message . 'name changed ';
            $product->setName($data['name']);
        }

        if($product->getDescription() !== $data['description']) {
            $isChanged = true;
            $message = $message . 'description changed ';
            $product->setDescription($data['description']);
        }

        if($isChanged) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            return [$isChanged, $message];
        }

        return [$isChanged, 'no fields changed'];
    }
}