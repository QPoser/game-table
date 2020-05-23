<?php
declare(strict_types=1);

namespace App\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait TimeStampTrait
{
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Groups({"Default", "Api"})
     */
    private ?DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Groups({"Default", "Api"})
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTime('now');
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime('now');
        }
        $this->statusHistory .= PHP_EOL . $this->status;
    }
}