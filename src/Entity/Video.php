<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
  use CreatedAtTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'videos')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE', name: 'id_trick')]
  private ?Trick $trick = null;

  #[ORM\Column(length: 300, unique: true)]
  private ?string $path = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTrick(): ?Trick
  {
    return $this->trick;
  }

  public function setTrick(?Trick $trick): self
  {
    $this->trick = $trick;

    return $this;
  }

  public function getPath(): ?string
  {
      return $this->path;
  }

  public function setPath(string $path): self
  {
      $this->path = $path;

      return $this;
  }
}
