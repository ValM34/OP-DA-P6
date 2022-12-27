<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Trick;
use App\Entity\Message;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\TrickServiceInterface;
use App\Service\ImageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CreationTrick;
use App\Form\UpdateTrickForm;
use App\Form\CreationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickController extends AbstractController
{
  use CreatedAtTrait;

  private $trickService;
  private $imageService;

  public function __construct(TrickServiceInterface $trickService, ImageServiceInterface $imageService)
  {
    $this->trickService = $trickService;
    $this->imageService = $imageService;
    $this->request = new Request(
      $_GET,
      $_POST,
      [],
      $_COOKIE,
      $_FILES,
      $_SERVER
    );
  }

  // Nom des methods : 
  // displayAll, displayOne, create, update, delete

  // STYLE GUIDE
  #[Route('/styleguide', name: 'style_guide')]
  public function styleGuide(): Response
  {
    return $this->render('styleGuide.html.twig');
  }

  // DISPLAY ALL
  #[Route('/', name: 'home')]
  public function displayAll(): Response
  {
    $tricks = $this->trickService->findAll();
    $tricksData = [];

    for($i = 0; $i < count($tricks); $i++){
      $tricksData[$i]['id'] = $tricks[$i]->getId();
      $tricksData[$i]['name'] = $tricks[$i]->getName();
      if($tricks[$i]->getImages()[0] !== null){
        $tricksData[$i]['path'] = $tricks[$i]->getImages()[0]->getPath();
      } else {
        $tricksData[$i]['path'] = 'images/defaultImage.jpg';
      }
    }

    if(isset($_GET['succesMessage'])){
      $succesMessage = $this->request->get('succesMessage');
    } else {
      $succesMessage = null;
    }

    return $this->render('home/home.html.twig', [
      'tricks' => $tricksData,
      'succesMessage' => $succesMessage
    ]);
  }

  // DISPLAY ONE
  #[Route('/trick/displayone/{id}', name: 'trick_display_one')]
  public function displayOne(int $id, string $succesMessage = null, Request $request): Response
  {
    $message = new Message();
    $form = $this->createForm(CreationMessage::class, $message);
    $form->handleRequest($request);
    $trick = $this->trickService->findOne($id);
    $messages = $this->trickService->getMessages($id);

    $messagesArray = [];
    $arr = [];
    for($i = 0; $i < count($messages); $i++){
      $arr = [
        'id'=>$messages[$i]->getId(),
        'content'=>$messages[$i]->getContent(),
        'firstname'=>$messages[$i]->getUser()->getFirstname(),
        'lastname'=>$messages[$i]->getUser()->getLastName()]
      ;
      array_push($messagesArray, $arr);
    }

    // @TODO => problème n.1
    $imagesPaths = [];
    $imagePath = null;

    if($trick->getImages()[0] !== null){
      $imagePath = $trick->getImages()[0]->getPath();
      for($i = 0; $i < count($trick->getImages()); $i++){  
          $imagesPaths[$i] = $trick->getImages()[$i]->getPath();
      }      
    } else {
      $imagePath = 'images/defaultImage.jpg';
    }

    if ($form->isSubmitted() && $form->isValid()) {
      $this->trickService->createMessage($this->getUser(), $trick, $message);
      
      return $this->redirectToRoute('trick_display_one', [
        'id' => $trick->getId(),
        'succesMessage' => 'Votre commentaire a bien été ajouté!'
      ]);
    }

    $category['name'] = $trick->getCategory()->getName();
    if(isset($_GET['succesMessage'])){
      $succesMessage = $this->request->get('succesMessage');
    } else {
      $succesMessage = null;
    }
    
    return $this->render('trick/displayone.html.twig', [
      'trick' => $trick,
      'messages' => $messagesArray,
      'succesMessage' => $succesMessage,
      'createMessageForm' => $form->createView(),
      'imagesPaths' => $imagesPaths,
      'imagePath' => $imagePath,
      'category' => $category
    ]);
  }

  // CREATE
  #[Route('/trick/create', name: 'trick_create')]
  public function create(Request $request): Response
  {
    /*if(null === $this->getUser()){
      // @TODO mettre en place le voter => update : je vérifie en-dessous si l'utilisateur est connecté avant de créer le trick.
      echo 'Veuillez vous connecter pour pouvoir ajouter un nouveau trick :';
      exit();
    }*/

    $trick = new Trick();
    $form = $this->createForm(CreationTrick::class, $trick);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
      $trick = $this->trickService->create($this->getUser(), $trick);

      return $this->redirectToRoute('home', [
        '_fragment' => '#tricks-container', // @TODO j'essaie de renvoyer vers un ancre mais ça ne fonctionne pas
        'succesMessage' => 'Votre trick a bien été ajouté!'
      ]);
    }

    return $this->render('trick/create.html.twig', [
      'categories' => $this->trickService->findAllCategories(),
      'createTrickForm' => $form->createView()
    ]);
  }

  // UPDATE
  #[Route('/trick/update/{id}', name: 'trick_update')]
  public function update(int $id, Request $request): Response
  {
    $trick = $this->trickService->findOne($id);
    $form = $this->createForm(UpdateTrickForm::class, $trick);
    $form->handleRequest($request);
    
    if(($form->isSubmitted() && $form->isValid() && $this->getUser())){
      $trick = $this->trickService->update($trick);
      $this->redirectToRoute('trick_display_one', [
        'id' => $id
      ]);
    }

    $trick = $this->trickService->updatePage($id);

    return $this->render('trick/update.html.twig', [
      'trick' => $trick,
      'updateTrickForm' => $form->createView()
    ]);
  }

  // DELETE
  #[Route('/trick/delete/{id}', name: 'delete_trick')]
  public function delete(int $id): Response
  {
    if($this->getUser()){
      $this->trickService->delete($id);
      $succesMessage = 'Le trick a bien été supprimé!';
  
      return $this->redirectToRoute('home', [
        'succesMessage' => $succesMessage
      ]);
    }
    
    return $this->redirectToRoute('home');
  }







  



  // A ENVOYER DANS MESSAGES

  // UPDATE (à fusionner avec celle d'en bas et à envoyer dans message controller)
  #[Route('/message/updatepage/{id}', name: 'update_message_page')]
  public function updateMessagePage(int $id): Response
  {
    $entity = $this->trickService->updateMessagePage($id);

    return $this->render('message/updatePage.html.twig', [
      'trick' => $entity['trick'],
      'message' => $entity['message']
    ]);
  }

  // UPDATE
  #[Route('/message/update/{id}', name: 'update_message_update')]
  public function updateMessage(int $id): Response
  {
    $message = $this->trickService->updateMessage($id);
    $trick = $message->getTrick();
    $messages = $this->trickService->getMessages($trick->getId());

    return $this->redirectToRoute('trick_display_one', [
      'trick' => $trick,
      'messages' => $messages,
      'id' => $message->getTrick()->getId()
    ]);
  }

  // DELETE (à envoyer dans messageController)
  #[Route('/message/delete/{id}', name: 'update_message_delete')]
  public function deleteMessage(int $id): Response
  {
    $message = $this->trickService->deleteMessage($id);
    $trick = $message->getTrick();
    $messages = $this->trickService->getMessages($trick->getId());

    return $this->redirectToRoute('trick_display_one', [
      'trick' => $trick,
      'messages' => $messages,
      'id' => $message->getTrick()->getId()
    ]);
  }
}
