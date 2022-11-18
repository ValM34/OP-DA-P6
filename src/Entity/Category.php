<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
  use CreatedAtTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id;

  #[ORM\Column(length: 150)]
  private ?string $name = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $updated_at = null;

  #[ORM\OneToMany(mappedBy: 'category', targetEntity: Trick::class)]
  private Collection $tricks;

  #[ORM\ManyToOne(inversedBy: 'categories')]
  #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL', name: 'id_user')]
  private ?User $user = null;

  #[ORM\Column(length: 1000)]
  private ?string $description = null;

  public function __construct()
  {
    $this->tricks = new ArrayCollection();
    $this->created_at = new \DateTimeImmutable();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

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

  /**
   * @return Collection<int, Trick>
   */
  public function getTricks(): Collection
  {
    return $this->tricks;
  }

  public function addTrick(Trick $trick): self
  {
    if (!$this->tricks->contains($trick)) {
      $this->tricks->add($trick);
      $trick->setCategory($this);
    }

    return $this;
  }

  public function removeTrick(Trick $trick): self
  {
    if ($this->tricks->removeElement($trick)) {
      // set the owning side to null (unless already changed)
      if ($trick->getCategory() === $this) {
        $trick->setCategory(null);
      }
    }

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

  public function getDescription(): ?string
  {
      return $this->description;
  }

  public function setDescription(string $description): self
  {
      $this->description = $description;

      return $this;
  }
}
