<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Entities\Category;
use App\Entities\Manufacturer;
use App\Entities\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ProductListener implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $args) {
        // TODO: this only handles the newly assigned category, what about the old category, how to decrement it's counter ???
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        if($entity instanceof Product) {
            $category = $entity->getCategory();
            if($category) {
                $category->incrementProductCount(1);
                $entityManager->persist($category);
            }

            $manufacturer = $entity->getManufacturer();
            if($manufacturer) {
                $manufacturer->incrementProductCount(1);
                $entityManager->persist($manufacturer);
            }

            // TODO: activate `cascade: ['persist']` in product-category relationship
            $entityManager->flush();
        }
    }

    public function postUpdate(PostUpdateEventArgs $args) {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        if($entity instanceof Product) {
            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($entity);
            $isUpdate = false;

            // handle category update
            if(isset($changeSet['category'])) {
                $isUpdate = true;

                /** @var Category $oldCategory */
                $oldCategory = $changeSet['category'][0];
                if($oldCategory) {
                    $oldCategory->decrementProductCount(1);
                    $entityManager->persist($oldCategory);
                }

                /** @var Category $newCategory */
                $newCategory = $changeSet['category'][1];
                if($newCategory) {
                    $newCategory->incrementProductCount(1);
                    $entityManager->persist($newCategory);
                }

            }

            // handle category update
            if(isset($changeSet['manufacturer'])) {
                $isUpdate = true;

                /** @var Manufacturer $oldManufacturer */
                $oldManufacturer = $changeSet['manufacturer'][0];
                if($oldManufacturer) {
                    $oldManufacturer->decrementProductCount(1);
                    $entityManager->persist($oldManufacturer);
                }

                /** @var Manufacturer $newManufacturer */
                $newManufacturer = $changeSet['manufacturer'][1];
                if($newManufacturer) {
                    $newManufacturer->incrementProductCount(1);
                    $entityManager->persist($newManufacturer);
                }

            }

            if($isUpdate) {
                $entityManager->flush();
            }
        }
    }

    public function postRemove(LifecycleEventArgs $args) {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        if($entity instanceof Product) {
            $category = $entity->getCategory();
            if($category) {
                $category->decrementProductCount(1);
                $entityManager->persist($category);
            }

            $manufacturer = $entity->getManufacturer();
            if($manufacturer) {
                $manufacturer->decrementProductCount(1);
                $entityManager->persist($manufacturer);
            }

            // TODO: activate `cascade: ['persist']` in product-category relationship
            $entityManager->flush();
        }
    }
}