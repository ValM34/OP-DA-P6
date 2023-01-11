<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Message;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request; 

class MessageService implements MessageServiceInterface
{
  private $entityManager;
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
    $this->request = new Request();
  }

  // FIND BY TRICK
  public function findByTrick(int $trick)
  {
    return $this->entityManager->getRepository(Message::class)->findByTrick($trick, ['created_at' => 'DESC']);
  }

  // FIND BY ID
  public function findById(int $id)
  {
    return $this->entityManager->getRepository(Message::class)->findOneBy(['id' => $id]);
  }

  // CREATE
  public function create(User $user, Trick $trick, Message $message): void
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

  // UPDATE PAGE
  public function updatePage(int $id)
  {
    $entity['message'] = $this->entityManager->getRepository(Message::class)->find($id);
    $entity['trick'] = $entity['message']->getTrick();

    return $entity;
  }

  // UPDATE
  public function update(int $id)
  {
    $entity = $this->entityManager->getRepository(Message::class)->find($id);
    $entity->setUpdatedAt(new \DateTimeImmutable());
    if ($this->request->request->get('content')) {
      $entity->setContent($this->request->request->get('content'));
    };
    $this->entityManager->persist($entity);
    $this->entityManager->flush();

    return $entity;
  }

  // DELETE
  public function delete(int $id)
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
