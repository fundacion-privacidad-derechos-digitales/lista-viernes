<?php
namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 */
trait Timestampable {

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Timestampable
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return Timestampable
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}