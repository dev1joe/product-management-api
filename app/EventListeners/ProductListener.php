<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Entities\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ProductListener implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
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