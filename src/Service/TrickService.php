<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Category;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request; 

class TrickService implements TrickServiceInterface
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

  public function findAll()
  {
    return $this->entityManager->getRepository(Trick::class)->findAll();
  }

  public function findOne(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);
    if (!$trick) {
      $trick = null;
    }

    return $trick;
  }


















  public function create($user, Trick $trick)
  {
    $date = new DateTimeImmutable();
    $trick
      ->setUser($user)
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
    ;

    $this->entityManager->persist($trick);
    $this->entityManager->flush();

    return $trick;
  }

  

  public function updatePage(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);

    if (!$trick) {
      $trick = null;
    }

    return $trick;
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

  public function createMessage($user, $trick, $message)
  {
    $dateNow = new DateTimeImmutable();

    $message
      ->setUser($user)
      ->setTrick($trick)
      ->setUpdatedAt($dateNow)
      ->setCreatedAt($dateNow)
    ;

    $this->entityManager->persist($message);
    $this->entityManager->flush();
  }

  public function getMessages($trick)
  {
    return $this->entityManager->getRepository(Message::class)->findByTrick($trick);
  }

  public function findAllCategories()
  {
    return $this->entityManager->getRepository(Category::class)->findAll();
  }

  public function updateMessagePage(int $id)
  {
    $entity['message'] = $this->entityManager->getRepository(Message::class)->find($id);
    $entity['trick'] = $entity['message']->getTrick();

    if (!$entity['message']) {
      $message = null;
    }

    return $entity;
  }

  public function updateMessage(int $id)
  {
    $entity = $this->entityManager->getRepository(Message::class)->find($id);
    $entity
      ->setUpdatedAt(new \DateTimeImmutable())
      ->setCreatedAt(new \DateTimeImmutable());
    if ($this->request->request->get('content')) {
      $entity->setContent($this->request->request->get('content'));
    };
    $this->entityManager->persist($entity);
    $this->entityManager->flush();

    return $entity;
  }

  public function deleteMessage(int $id)
  {
    $entity = $this->entityManager->getRepository(Message::class)->find($id);
    $entity
      ->setUpdatedAt(new \DateTimeImmutable())
      ->setCreatedAt(new \DateTimeImmutable());
    if ($this->request->request->get('content')) {
      $entity->setContent($this->request->request->get('content'));
    };
    $this->entityManager->remove($entity);
    $this->entityManager->flush();

    return $entity;
  }
}
