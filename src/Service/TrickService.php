<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;

class TrickService implements TrickServiceInterface
{
  private $entityManager;
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function create()
  {
    $this->created_at = new DateTimeImmutable();

    $trick = new Trick();
    $trick
      ->setName($_POST['name'])
      ->setDescription($_POST['description'])
      ->setUpdatedAt($this->created_at)
      ->setCreatedAt($this->created_at);
    $user = $this->entityManager->getRepository(User::class)->findOneById(1);
    $category = $this->entityManager->getRepository(Category::class)->findOneById(1);
    $trick->setIdUser($user);
    $trick->setIdCategory($category);

    // tell Doctrine you want to (eventually) save the Product (no queries yet)
    $this->entityManager->persist($trick);

    // actually executes the queries (i.e. the INSERT query)
    $this->entityManager->flush();

    return $trick;
  }

  public function readAll()
  {
    $trick = $this->entityManager->getRepository(Trick::class)->findAll();
    if (!$trick) {
      $trick = null;

      return $trick;
    }
    return $trick;
  }

  public function update(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);

    if (!$trick) {
      $trick = null;

      return $trick;
    }
    if ($_POST) {
      if ($_POST['name']) {
        $trick->setName($_POST['name']);
      };
      if ($_POST['description']) {
        $trick->setDescription($_POST['description']);
      };
    };
    $this->entityManager->flush();

    return $trick;
  }
}
