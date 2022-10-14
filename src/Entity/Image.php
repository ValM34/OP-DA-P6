<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
  use CreatedAtTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'images')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Trick $id_trick = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $updated_at = null;

  public function __construct()
  {
    $this->created_at = new \DateTimeImmutable();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getIdTrick(): ?Trick
  {
    return $this->id_trick;
  }

  public function setIdTrick(?Trick $id_trick): self
  {
    $this->id_trick = $id_trick;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updated_at;
  }

  public function setUpdatedAt(\DateTimeImmutable $updated_at): self
  {
    $this->updated_at = $updated_at;

    return $this;
  }
}
