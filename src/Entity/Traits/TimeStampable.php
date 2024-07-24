<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TimeStampable
{
    use TimezoneConverterHelper;

    /**
     * @var ?\DateTimeImmutable
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeImmutable $createdAt = null;

    /**
     * @var ?\DateTimeImmutable
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @throws \Exception
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->convertCreatedAtDateTimeToCST();
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        if (null == $this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        } else {
            $this->setUpdatedAt(new \DateTimeImmutable('now'));
        }
    }

    /**
     * @throws \Exception
     */
    public function convertCreatedAtDateTimeToUTC(): ?\DateTimeImmutable
    {
        return $this->convertDateTimeToUTC($this->createdAt);
    }

    /**
     * @throws \Exception
     */
    public function convertCreatedAtDateTimeToCST(): ?\DateTimeImmutable
    {
        return $this->convertDateTimeToCST($this->createdAt);
    }
}
