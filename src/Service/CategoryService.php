<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Category;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryService implements CategoryServiceInterface
{
  private $entityManager;
  private $dateTimeImmutable;

  public function __construct(EntityManagerInterface $entityManager, protected SluggerInterface $slugger)
  {
    $this->entityManager = $entityManager;
    $this->dateTimeImmutable = new DateTimeImmutable();
  }

  // FIND ALL
  public function findAll(): array
  {
    return $this->entityManager->getRepository(Category::class)->findAll();
  }

  // CREATE
  public function create(User $user, Category $category): void
  {
    $date = new DateTimeImmutable();
    $category
      ->setUser($user)
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
      ->setSlug($this->slugger->slug($category->getName()))
    ;
    $this->entityManager->persist($category);
    $this->entityManager->flush();
  }

  // UPDATE
  public function update(Category $category): void
  {
    $category
      ->setUpdatedAt($this->dateTimeImmutable)
      ->setSlug($this->slugger->slug($category->getName()))
    ;
    $this->entityManager->persist($category);
    $this->entityManager->flush();
  }

  // DELETE
  public function delete($category): void
  {
    $this->entityManager->remove($category);
    $this->entityManager->flush();
  }
}
