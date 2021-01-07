<?php

namespace App\Entity\Traits;


trait Timestampable
{
  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   */
  private $createdAt;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   */
  private $updatedAt;

  public function getCreatedAt(): ?\DateTimeInterface
  {
      return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeInterface $createdAt): self
  {
      $this->createdAt = $createdAt;

      return $this;
  }

  public function getupdatedAt(): ?\DateTimeInterface
  {
      return $this->updatedAt;
  }

  public function setupdatedAt(\DateTimeInterface $updatedAt): self
  {
      $this->updatedAt = $updatedAt;

      return $this;
  }

  /**
   * @ORM\PrePersist
   * @ORM\PreUpdate
   */
  public function updateTimestamps()
  {
    if ($this->getCreatedAt() === null) {
      $this->setCreatedAt(new \DateTimeImmutable);
    }
    $this->setupdatedAt(new \DateTimeImmutable);
  }
}
