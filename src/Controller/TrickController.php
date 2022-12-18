<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\TrickServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CreationTrick;
use App\Form\CreationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickController extends AbstractController
{
  use CreatedAtTrait;

  private $trickService;

  public function __construct(TrickServiceInterface $trickService)
  {
    $this->trickService = $trickService;
  }

  // STYLE GUIDE
  #[Route('/styleguide', name: 'style_guide')]
  public function styleGuide(): Response
  {
    return $this->render('styleGuide.html.twig');
  }

  // DISPLAY ALL
  #[Route('/trick/all', name: 'trick_show_all')]
  public function showAll(): Response
  {
    return $this->render('trick/index.html.twig', [
      'tricks' => $this->trickService->findAll()
    ]);
  }

  // DISPLAY ONE
  #[Route('/trick/showone/{id}', name: 'trick_show')]
  public function show(int $id, string $succesMessage = null, Request $request): Response
  {
    $message = new Message();
    $form = $this->createForm(CreationMessage::class, $message);
    $form->handleRequest($request);
    $trick = $this->trickService->getOne($id);
    $messages = $this->trickService->getMessages($id);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->trickService->createMessage($this->getUser(), $trick, $message);
      
      // do anything else you need here, like send an email

      // Ici je dois retourner la page du trick que je viens de créer
      return $this->redirectToRoute('trick_show', [
        'id' => $trick->getId(),
        'succesMessage' => 'Votre commentaire a bien été ajouté!'
      ]);
    }
    $succesMessage = null;
    return $this->render('trick/showone.html.twig', [
      'trick' => $trick,
      'messages' => $messages,
      'succesMessage' => $succesMessage,
      'registrationForm' => $form->createView()
    ]);
  }

  #[Route('/trick/create', name: 'create_trick')]
  public function createProduct(Request $request): Response
  {
    if(null === $this->getUser()){
      // @TODO mettre en place le voter
      echo 'Veuillez vous connecter pour pouvoir ajouter un nouveau trick :';
      exit();
    }

    $trick = new Trick();
    $form = $this->createForm(CreationTrick::class, $trick);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $trick = $this->trickService->create($this->getUser(), $trick);

      return $this->redirectToRoute('trick_show', [
        'id' => $trick->getId(),
        'succesMessage' => 'Votre trick a bien été ajouté!'
      ]);
    }

    return $this->render('trick/creationPage.html.twig', [
      'categories' => $this->trickService->findAllCategories(),
      'registrationForm' => $form->createView()
    ]);
  }

  #[Route('/trick/updatepage/{id}', name: 'update_trick_page')]
  public function updateTrickPage(int $id): Response
  {
    $trick = $this->trickService->updatePage($id);

    return $this->render('trick/updatePage.html.twig', [
      'trick' => $trick,
    ]);
  }

  #[Route('/trick/update/{id}', name: 'update_trick')]
  public function updateTrick(int $id): Response
  {
    $trick = $this->trickService->update($id);
    $messages = $this->trickService->getMessages($id);

    return $this->render('trick/showone.html.twig', [
      'trick' => $trick,
      'messages' => $messages
    ]);
  }

  // DELETE
  #[Route('/trick/delete/{id}', name: 'delete_trick')]
  public function deleteTrick(int $id): Response
  {
    $this->trickService->delete($id);
    $tricks = $this->trickService->findAll();
    $succesMessage = 'Le trick a bien été supprimé!';

    return $this->render('trick/index.html.twig', [
      'controller_name' => 'TrickController',
      'tricks' => $tricks,
      'succesMessage' => $succesMessage
    ]);
  }

  /*#[Route('/message/create/{id}', name: 'create_message')]
  public function createMessage(int $id, Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
  {
    //$message = $this->trickService->createMessage($id);
    //$messages = $this->trickService->getMessages($message->getTrick()->getId());
    
    
    $message = new Message();
    $form = $this->createForm(CreationMessage::class, $message);
    $form->handleRequest($request);
    $trick = $this->trickService->getOne($id);
    $messages = $this->trickService->getMessages($id);

    if ($form->isSubmitted() && $form->isValid()) {
      $message
        ->setUser($user)
        ->setTrick($trick)
        ->setUpdatedAt(new DateTimeImmutable())
      ;
      $entityManager->persist($message);
      $entityManager->flush();
      // do anything else you need here, like send an email

      // Ici je dois retourner la page du trick que je viens de créer
      return $this->redirectToRoute('trick_show', [
        'id' => $trick->getId(),
        'succesMessage' => 'Votre trick a bien été ajouté!'
      ]);
    }
    $succesMessage = null;
    return $this->render('trick/showone.html.twig', [
      'trick' => $trick,
      'messages' => $messages,
      'succesMessage' => $succesMessage,
      'registrationForm' => $form->createView()
    ]);
  }*/

  #[Route('/message/updatepage/{id}', name: 'update_message_page')]
  public function updateMessagePage(int $id): Response
  {
    $entity = $this->trickService->updateMessagePage($id);

    return $this->render('message/updatePage.html.twig', [
      'trick' => $entity['trick'],
      'message' => $entity['message']
    ]);
  }

  #[Route('/message/update/{id}', name: 'update_message_update')]
  public function updateMessage(int $id): Response
  {
    $message = $this->trickService->updateMessage($id);
    $trick = $message->getTrick();
    $messages = $this->trickService->getMessages($trick->getId());

    return $this->redirectToRoute('trick_show', [
      'trick' => $trick,
      'messages' => $messages,
      'id' => $message->getTrick()->getId()
    ]);
  }

  #[Route('/message/delete/{id}', name: 'update_message_delete')]
  public function deleteMessage(int $id): Response
  {
    $message = $this->trickService->deleteMessage($id);
    $trick = $message->getTrick();
    $messages = $this->trickService->getMessages($trick->getId());

    return $this->redirectToRoute('trick_show', [
      'trick' => $trick,
      'messages' => $messages,
      'id' => $message->getTrick()->getId()
    ]);
  }
}
