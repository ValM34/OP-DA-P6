<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  use CreatedAtTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 180, unique: true)]
  private ?string $email = null;

  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string The hashed password
   */
  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column(length: 100)]
  private ?string $lastname = null;

  #[ORM\Column(length: 100)]
  private ?string $firstname = null;

  #[ORM\Column(length: 100)]
  private ?string $pseudo = null;

  #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
  private ?\DateTimeImmutable $updated_at = null;

  #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Trick::class)]
  private Collection $tricks;

  #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Message::class)]
  private Collection $messages;

  public function __construct()
  {
    $this->tricks = new ArrayCollection();
    $this->messages = new ArrayCollection();
    $this->created_at = new \DateTimeImmutable();
    $this->updated_at = new \DateTimeImmutable();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials()
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  public function getLastname(): ?string
  {
    return $this->lastname;
  }

  public function setLastname(string $lastname): self
  {
    $this->lastname = $lastname;

    return $this;
  }

  public function getFirstname(): ?string
  {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): self
  {
    $this->firstname = $firstname;

    return $this;
  }

  public function getPseudo(): ?string
  {
    return $this->pseudo;
  }

  public function setPseudo(string $pseudo): self
  {
    $this->pseudo = $pseudo;

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
      $trick->setIdUser($this);
    }

    return $this;
  }

  public function removeTrick(Trick $trick): self
  {
    if ($this->tricks->removeElement($trick)) {
      // set the owning side to null (unless already changed)
      if ($trick->getIdUser() === $this) {
        $trick->setIdUser(null);
      }
    }

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
      $message->setIdUser($this);
    }

    return $this;
  }

  public function removeMessage(Message $message): self
  {
    if ($this->messages->removeElement($message)) {
      // set the owning side to null (unless already changed)
      if ($message->getIdUser() === $this) {
        $message->setIdUser(null);
      }
    }

    return $this;
  }
}
