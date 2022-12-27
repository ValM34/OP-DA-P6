<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity(
  fields: ['name'],
  message: 'Ce trick existe déjà.',
)]
class Trick
{
  use CreatedAtTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(name: 'name', length: 150, type: 'string', unique: true)]
  private ?string $name = null;

  #[ORM\Column(length: 2500)]
  private ?string $description = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $updated_at = null;

  #[ORM\ManyToOne(inversedBy: 'tricks')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE', name: 'id_user')]
  private ?User $user = null;

  #[ORM\ManyToOne(inversedBy: 'tricks')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE', name: 'id_category')]
  private ?Category $category = null;

  #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Message::class)]
  private Collection $messages;

  #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Image::class)]
  private Collection $images;

  #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Video::class)]
  private Collection $videos;

  public function __construct()
  {
    $this->messages = new ArrayCollection();
    $this->images = new ArrayCollection();
    $this->videos = new ArrayCollection();
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

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): self
  {
    $this->description = $description;

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

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): self
  {
    $this->user = $user;

    return $this;
  }

  public function getCategory(): ?Category
  {
    return $this->category;
  }

  public function setCategory(?Category $category): self
  {
    $this->category = $category;

    return $this;
  }

  /**
   * @return Collection<int, Message>
   */
  public function getMessages(): Collection
  {
    return $this->messages;
  }

  public function addMessage(Message $message): self
  {
    if (!$this->messages->contains($message)) {
      $this->messages->add($message);
      $message->setTrick($this);
    }

    return $this;
  }

  public function removeMessage(Message $message): self
  {
    if ($this->messages->removeElement($message)) {
      // set the owning side to null (unless already changed)
      if ($message->getTrick() === $this) {
        $message->setTrick(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Image>
   */
  public function getImages(): Collection
  {
    return $this->images;
  }

  public function addImage(Image $image): self
  {
    if (!$this->images->contains($image)) {
      $this->images->add($image);
      $image->setTrick($this);
    }

    return $this;
  }

  public function removeImage(Image $image): self
  {
    if ($this->images->removeElement($image)) {
      // set the owning side to null (unless already changed)
      if ($image->getTrick() === $this) {
        $image->setTrick(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Video>
   */
  public function getVideos(): Collection
  {
    return $this->videos;
  }

  public function addVideo(Video $video): self
  {
    if (!$this->videos->contains($video)) {
      $this->videos->add($video);
      $video->setTrick($this);
    }

    return $this;
  }

  public function removeVideo(Video $video): self
  {
    if ($this->videos->removeElement($video)) {
      // set the owning side to null (unless already changed)
      if ($video->getTrick() === $this) {
        $video->setTrick(null);
      }
    }

    return $this;
  }
}
