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

  public function create()
  {
    $this->created_at = new DateTimeImmutable();

    $trick = new Trick();
    $trick
      ->setName($this->request->request->get('name'))
      ->setDescription($this->request->request->get('description'))
      ->setUpdatedAt($this->created_at)
      ->setCreatedAt($this->created_at);
    $user = $this->entityManager->getRepository(User::class)->findOneById(1);
    $category = $this->entityManager->getRepository(Category::class)->findOneById(1);
    $trick->setUser($user);
    $trick->setCategory($category);

    // tell Doctrine you want to (eventually) save the Product (no queries yet)
    $this->entityManager->persist($trick);

    // actually executes the queries (i.e. the INSERT query)
    $this->entityManager->flush();

    return $trick;
  }

  public function findAll()
  {
    return $this->entityManager->getRepository(Category::class)->findAll();
  }

  public function getOne(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);
    if (!$trick) {
      $trick = null;
    }

    return $trick;
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
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);

    if (!$trick) {
      $trick = null;
    }
    if ($this->request->request->get('name')) {
      $trick->setName($this->request->request->get('name'));
    };
    if ($this->request->request->get('description')) {
      $trick->setDescription($this->request->request->get('description'));
    };
    $this->entityManager->flush();

    return $trick;
  }

  public function delete(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);

    if (!$trick) {
      $trick = null;
    }

    $this->entityManager->remove($trick);
    $this->entityManager->flush();

    return $trick;
  }
}
