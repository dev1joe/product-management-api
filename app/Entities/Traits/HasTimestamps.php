<?php
declare(strict_types=1);

namespace App\Entities\Traits;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\Persistence\Event\ManagerEventArgs;

trait HasTimestamps
{
    #[Column(name: 'created_at', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $createdAt;
    #[Column(name: 'updated_at', nullable: true, options: ['default' => null])]
    private ?\DateTime $updatedAt;

    #[PreUpdate, PrePersist]
    public function updateTimestamps(ManagerEventArgs $args): void {
        if(! isset($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        $this->updatedAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}