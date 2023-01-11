<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;

class CategoryService implements CategoryServiceInterface
{
  private $entityManager;
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  // FIND ALL
  public function findAll(): array
  {
    return $this->entityManager->getRepository(Category::class)->findAll();
  }

  // FIND ONE
  public function findOne(int $id): Category
  {
    $category = $this->entityManager->getRepository(Category::class)->find($id);
    if (!$category) {
      $category = null;
    }

    return $category;
  }

  // CREATE
  public function create(User $user, Category $category): void
  {
    $date = new DateTimeImmutable();
    $category
      ->setUser($user)
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
    ;
    $this->entityManager->persist($category);
    $this->entityManager->flush();
  }

  // UPDATE PAGE
  public function updatePage(int $id): Category
  {
    $category = $this->entityManager->getRepository(Category::class)->find($id);
    if (!$category) {
      $category = null;
    }

    return $category;
  }

  public function update(int $id): void
  {
    $category = $this->entityManager->getRepository(Category::class)->find($id);
    $this->entityManager->persist($category);
    $this->entityManager->flush();
  }

  public function delete(int $id): void
  {
    $category = $this->entityManager->getRepository(Category::class)->find($id);
    $this->entityManager->remove($category);
    $this->entityManager->flush();
  }
}
