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
  private $request;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
    $this->request = new Request();
  }

  // FIND BY TRICK
  public function findByTrick(int $trick, ?User $user): array
  {
    $messages = $this->entityManager->getRepository(Message::class)->findByTrick($trick, ['created_at' => 'DESC']);
    $messagesArray = [];
    $arr = [];
    for ($i = 0, $count = count($messages); $i < $count; $i++) {
      $arr = [
        'id' => $messages[$i]->getId(),
        'content' => $messages[$i]->getContent(),
        'firstname' => $messages[$i]->getUser()->getFirstname(),
        'lastname' => $messages[$i]->getUser()->getLastName(),
        'avatar' => $messages[$i]->getUser()->getAvatar()
      ];
      if ($user) {
        if ($messages[$i]->getUser() === $user) {
          $arr = array_merge($arr, ['isOwner' => true]);
        } else {
          $arr = array_merge($arr, ['isOwner' => false]);
        }
      } else {
        $arr = array_merge($arr, ['isOwner' => false]);
      }
      array_push($messagesArray, $arr);
    }
    
    return $messagesArray;
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
  public function updatePage(Message $message): array
  {
    $entity['message'] = $message;
    $entity['trick'] = $entity['message']->getTrick();

    return $entity;
  }

  // UPDATE
  public function update(Message $message): Message
  {
    $message->setUpdatedAt(new \DateTimeImmutable());
    if ($this->request->request->get('content')) {
      $message->setContent($this->request->request->get('content'));
    };
    $this->entityManager->persist($message);
    $this->entityManager->flush();

    return $message;
  }

  // DELETE
  public function delete(int $id): void
  {
    $entity = $this->entityManager->getRepository(Message::class)->find($id);
    $this->entityManager->remove($entity);
    $this->entityManager->flush();
  }
}
