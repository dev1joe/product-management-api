<?php
declare(strict_types=1);

namespace App\Entities\Traits;

use Doctrine\ORM\Mapping\Column;

trait HasSoftDelete
{
    #[Column(name: 'deleted_at', nullable: true)]
    private ?\DateTime $deletedAt;

    public function delete(): void {
        $this->deletedAt = new \DateTime();
    }

    public function undoDelete(): void {
        $this->deletedAt = null;
    }

    public function getDeletedAt(): \DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}