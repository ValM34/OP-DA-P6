<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
  use CreatedAtTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'messages')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE', name: 'id_trick')]
  private ?Trick $trick = null;

  #[ORM\ManyToOne(inversedBy: 'messages')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE', name: 'id_user')]
  private ?User $user = null;

  #[ORM\Column(length: 2500)]
  private ?string $content = null;

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

  public function getTrick(): ?Trick
  {
    return $this->trick;
  }

  public function setTrick(?Trick $trick): self
  {
    $this->trick = $trick;

    return $this;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): self
  {
    $this->user = $user;

    return $this;
  }

  public function getContent(): ?string
  {
    return $this->content;
  }

  public function setContent(string $content): self
  {
    $this->content = $content;

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
