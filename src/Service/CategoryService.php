<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

class CategoryService implements CategoryServiceInterface
{
  private $entityManager;
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
    $this->request = new Request(
      $_GET,
      $_POST,
      [],
      $_COOKIE,
      $_FILES,
      $_SERVER
    );
  }

  // FIND ALL
  public function findAll()
  {
    return $this->entityManager->getRepository(Category::class)->findAll();
  }

  // FIND ONE
  public function findOne(int $id)
  {
    $category = $this->entityManager->getRepository(Category::class)->find($id);
    if (!$category) {
      $category = null;
    }

    return $category;
  }

  // CREATE
  public function create($user, Category $category)
  {
    $this->date = new DateTimeImmutable();
    $category
      ->setUser($user)
      ->setUpdatedAt($this->date)
      ->setCreatedAt($this->date)
    ;
    $this->entityManager->persist($category);
    $this->entityManager->flush();

    return $category;
  }

  public function updatePage(int $id)
  {
    $category = $this->entityManager->getRepository(Category::class)->find($id);
    if (!$category) {
      $category = null;
    }

    return $category;
  }

  public function update(int $id)
  {
    $category = $this->entityManager->getRepository(Category::class)->find($id);
    $this->entityManager->persist($category);
    $this->entityManager->flush();

    return $category;
  }

  public function delete(int $id)
  {
    $category = $this->entityManager->getRepository(Category::class)->find($id);
    $this->entityManager->remove($category);
    $this->entityManager->flush();
  }
}
