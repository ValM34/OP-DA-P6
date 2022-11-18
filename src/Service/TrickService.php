<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\User;
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

  public function create()
  {
    $this->created_at = new DateTimeImmutable();

    $trick = new Trick();
    $trick
      ->setName($this->request->request->get('name'))
      ->setDescription($this->request->request->get('description'))
      ->setUpdatedAt($this->created_at)
      ->setCreatedAt($this->created_at)
    ;
    $user = $this->entityManager->getRepository(User::class)->findOneById(1);
    $category = $this->entityManager->getRepository(Category::class)->findOneById($this->request->request->get('category'));
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
    return $this->entityManager->getRepository(Trick::class)->findAll();
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

  public function createMessage($id_trick)
  {
    $this->created_at = new DateTimeImmutable();

    $message = new Message();
    $message
      ->setUpdatedAt($this->created_at)
      ->setCreatedAt($this->created_at)
    ;
    $user = $this->entityManager->getRepository(User::class)->findOneById(1);
    $trick = $this->entityManager->getRepository(Trick::class)->findOneById($id_trick);
    $message->setUser($user);
    $message->setTrick($trick);
    if ($this->request->request->get('content')) {
      $message->setContent($this->request->request->get('content'));
    };

    // tell Doctrine you want to (eventually) save the Product (no queries yet)
    $this->entityManager->persist($message);

    // actually executes the queries (i.e. the INSERT query)
    $this->entityManager->flush();

    return $message;
  }

  public function getMessages(int $id)
  {
    return $this->entityManager->getRepository(Message::class)->findByTrick($id);
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
      ->setCreatedAt(new \DateTimeImmutable())
    ;
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
      ->setCreatedAt(new \DateTimeImmutable())
    ;
    if ($this->request->request->get('content')) {
      $entity->setContent($this->request->request->get('content'));
    };
    $this->entityManager->remove($entity);
    $this->entityManager->flush();

    return $entity;
  }
}
