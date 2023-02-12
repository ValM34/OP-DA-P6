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
use App\Form\MessageForm;
use Symfony\Component\HttpFoundation\Request;
////////////////////////////////////////////////////////
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\TrickRepository;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;


class MessageController extends AbstractController
{
  use CreatedAtTrait;

  public function __construct(
    private UserServiceInterface $userService,
    private MessageServiceInterface $messageService,
    private TrickServiceInterface $trickService,
    private SerializerInterface $serializer
  ) {}

  // @TODO Créer un controller MessageApiController.php pour séparer l'API et le controller classique
  // GET MESSAGES BY TRICK
  #[Route('api/message/{slug}', requirements: ['slug' => '[a-zA-Z0-9\-]+'], name: 'app_display_messages', methods: ['GET'])]
  public function getCustomers(string $slug, TrickRepository $messageRepository, Request $request): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 5);
    $context = (new ObjectNormalizerContextBuilder())
      ->withGroups('get_messages')
      ->toArray()
    ;
    $customerList = $messageRepository->getMessages($slug, $page, $limit);
    $jsonCustomerList = $this->serializer->serialize($customerList, 'json', $context);
    
    if($this->getUser() !== null){
      $actualUser = $this->userService->findOne($this->getUser())->getId();
      $actualUserJson = ['actualUser' => $actualUser];
      $jsonCustomerList = json_encode(
        array_merge(
          json_decode($jsonCustomerList, true),
          $actualUserJson
        )
      );
    }
    
    if($customerList === null){
      
      return new JsonResponse('{"res": "null"}', Response::HTTP_OK, [], true);
    }

    return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
  }

  // @TODO mettre en place voter et modifier la méthode
  // UPDATE
  #[Route('/message/update/{id}', requirements: ['id' => '[0-9]+'], name: 'message_update', methods: ['GET', 'POST'])]
  public function update(Message $message, Request $request): Response
  {
    $entity = $this->messageService->updatePage($message);
    $form = $this->createForm(MessageForm::class, $message);
    $form->handleRequest($request);

    $owner = $entity['message']->getUser()->getId();
    $actualUser = $this->userService->findOne($this->getUser())->getId();

    if($form->isSubmitted() && $form->isValid() && $this->getUser() && $owner === $actualUser){
      $message = $this->messageService->update($message);
      $this->addFlash('succes', 'Votre message a bien été modifié.');
      
      return $this->redirectToRoute('trick_display_one', [
        'slug' => $message->getTrick()->getSlug()
      ]);
    }

    if($owner !== $actualUser){
      return $this->redirectToRoute('trick_display_one', [
        'slug' => $message->getTrick()->getSlug()
      ]);
    }

    return $this->render('message/update.html.twig', [
      'message' => $entity['message'],
      'messageForm' => $form->createView()
    ]);
  }

  // @TODO mettre en place voter et modifier la méthode
  // DELETE
  #[Route('/message/delete/{id}', name: 'message_delete', methods: ['GET'])]
  public function delete(Message $message): Response
  {
    $owner = $message->getUser();
    if($this->getUser() && $owner === $this->getUser()){
      $this->messageService->delete($message->getId());
      $trick = $message->getTrick();
      $this->addFlash('succes', 'Le message a bien été supprimé');
    } else {
      $this->addFlash('error', "Vous n'avez pas l'autorisation pour supprimer ce message");
      $trick = $message->getTrick();
    }

    return $this->redirectToRoute('trick_display_one', [
      'trick' => $trick,
      'slug' => $trick->getSlug()
    ]);
  }
}
