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
  #[ORM\JoinColumn(nullable: false)]
  private ?Trick $id_trick = null;

  #[ORM\ManyToOne(inversedBy: 'messages')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $id_user = null;

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

  public function getIdTrick(): ?Trick
  {
    return $this->id_trick;
  }

  public function setIdTrick(?Trick $id_trick): self
  {
    $this->id_trick = $id_trick;

    return $this;
  }

  public function getIdUser(): ?User
  {
    return $this->id_user;
  }

  public function setIdUser(?User $id_user): self
  {
    $this->id_user = $id_user;

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
