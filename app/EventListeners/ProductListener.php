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
        $entity = $args->getObject();

        if($entity instanceof Product) {
            $category = $entity->getCategory();
            $category->incrementProductCount(1);

            $manufacturer = $entity->getManufacturer();
            $manufacturer->incrementProductCount(1);

            $entityManager = $args->getObjectManager();
            $entityManager->persist($category);
            $entityManager->persist($manufacturer);
            // TODO: test if you need to flush or not
        }
    }

    public function postRemove(LifecycleEventArgs $args) {
        $entity = $args->getObject();

        if($entity instanceof Product) {
            $category = $entity->getCategory();
            $category->decrementProductCount(1);

            $manufacturer = $entity->getManufacturer();
            $manufacturer->decrementProductCount(1);

            $entityManager = $args->getObjectManager();
            $entityManager->persist($category);
            $entityManager->persist($manufacturer);
            // TODO: test if you need to flush or not
        }
    }
}