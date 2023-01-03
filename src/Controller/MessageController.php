<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\MessageServiceInterface;
use App\Service\TrickServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UpdateMessageFormType;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends AbstractController
{
  use CreatedAtTrait;

  private $messageService;
  private $trickService;
  private $userService;

  public function __construct(UserServiceInterface $userService, MessageServiceInterface $messageService, TrickServiceInterface $trickService)
  {
    $this->userService = $userService;
    $this->messageService = $messageService;
    $this->trickService = $trickService;
  }

  // UPDATE
  #[Route('/message/update/{id}', name: 'message_update')]
  public function update(int $id, Message $message, Request $request): Response
  {
    $entity = $this->messageService->updatePage($id);
    $form = $this->createForm(UpdateMessageFormType::class, $message);
    $form->handleRequest($request);

    $owner = $entity['message']->getUser()->getId();
    $actualUser = $this->userService->findOne($this->getUser())->getId();

    if($form->isSubmitted() && $form->isValid() && $this->getUser() && $owner === $actualUser){
      $message = $this->messageService->update($id);
      
      return $this->redirectToRoute('trick_display_one', [
        'succesMessage' => 'Votre message a bien été modifié.',
        'id' => $message->getTrick()->getId()
      ]);
    }

    if($owner !== $actualUser){
      return $this->redirectToRoute('trick_display_one', [
        'id' => $message->getTrick()->getId()
      ]);
    }

    return $this->render('message/update.html.twig', [
      'message' => $entity['message'],
      'updateMessageFormType' => $form->createView()
    ]);
  }

  // DELETE
  #[Route('/message/delete/{id}', name: 'message_delete')]
  public function delete(int $id): Response
  {
    $message = $this->messageService->findById($id);
    $owner = $message->getUser()->getId();
    if($this->getUser()){
      $actualUser = $this->userService->findOne($this->getUser())->getId();
    } else {
      $actualUser = null;
    }
    if($this->getUser() && $owner === $actualUser){
      $this->messageService->delete($id);
      $trick = $message->getTrick();
    } else {
      $trick = $message->getTrick();
    }
    $messages = $this->messageService->findByTrick($trick->getId());

    return $this->redirectToRoute('trick_display_one', [
      'trick' => $trick,
      'messages' => $messages,
      'id' => $trick->getId()
    ]);
  }
}
