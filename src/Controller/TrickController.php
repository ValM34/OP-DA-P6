<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Message;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\TrickServiceInterface;
use App\Service\ImageServiceInterface;
use App\Service\UserServiceInterface;
use App\Service\MessageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CreationTrick;
use App\Form\UpdateTrickForm;
use App\Form\MessageCreateType;
use Symfony\Component\HttpFoundation\Request;

class TrickController extends AbstractController
{
  use CreatedAtTrait;

  private $trickService;
  private $imageService;
  private $messageService;
  private $userService;

  public function __construct(TrickServiceInterface $trickService, MessageServiceInterface $messageService, ImageServiceInterface $imageService, UserServiceInterface $userService)
  {
    $this->trickService = $trickService;
    $this->imageService = $imageService;
    $this->userService = $userService;
    $this->messageService = $messageService;
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
        $tricksData[$i]['path'] = 'images/tricks/' . $tricks[$i]->getImages()[0]->getPath();
      } else {
        $tricksData[$i]['path'] = 'images/tricks/default.jpg';
      }
    }

    return $this->render('home/home.html.twig', [
      'tricks' => $tricksData
    ]);
  }

  // DISPLAY ONE
  #[Route('/trick/displayone/{id}', name: 'trick_display_one')]
  public function displayOne(int $id, Request $request): Response
  {
    $message = new Message();
    $form = $this->createForm(MessageCreateType::class, $message);
    $form->handleRequest($request);
    $trick = $this->trickService->findOne($id);
    $messages = $this->messageService->findByTrick($id);
    if($this->getUser()){
      $actualUser = $this->userService->findOne($this->getUser())->getId();
    } else {
      $actualUser = null;
    }
    $messagesArray = [];
    $arr = [];
    for($i = 0; $i < count($messages); $i++){
      if($this->getUser()){
        if($messages[$i]->getUser()->getId() === $actualUser){
          $arr = [
            'id'=>$messages[$i]->getId(),
            'content'=>$messages[$i]->getContent(),
            'firstname'=>$messages[$i]->getUser()->getFirstname(),
            'lastname'=>$messages[$i]->getUser()->getLastName(),
            'avatar'=>$messages[$i]->getUser()->getAvatar(),
            'isOwner'=>true]
          ;
        } else {
          $arr = [
            'id'=>$messages[$i]->getId(),
            'content'=>$messages[$i]->getContent(),
            'firstname'=>$messages[$i]->getUser()->getFirstname(),
            'lastname'=>$messages[$i]->getUser()->getLastName(),
            'avatar'=>$messages[$i]->getUser()->getAvatar(),
            'isOwner'=>false]
          ;
        }
      } else {
        $arr = [
          'id'=>$messages[$i]->getId(),
          'content'=>$messages[$i]->getContent(),
          'firstname'=>$messages[$i]->getUser()->getFirstname(),
          'lastname'=>$messages[$i]->getUser()->getLastName(),
          'avatar'=>$messages[$i]->getUser()->getAvatar(),
          'isOwner'=>false]
        ;
      }
      array_push($messagesArray, $arr);
    }

    $images = [];
    $imagePath = null;

    if($trick->getImages()[0] !== null){
      $imagePath = $trick->getImages()[0]->getPath();
      for($i = 0; $i < count($trick->getImages()); $i++){  
        $images[$i]['path'] = $trick->getImages()[$i]->getPath();
        $images[$i]['id'] = $trick->getImages()[$i]->getId();
      }
    } else {
      $imagePath = 'default.jpg';
    }

    $videos = [];
    if($trick->getVideos()[0] !== null){
      for($i = 0; $i < count($trick->getVideos()); $i++){  
          $videos[$i]['path'] = $trick->getVideos()[$i]->getPath();
          $videos[$i]['id'] = $trick->getVideos()[$i]->getId();
      }      
    }

    if ($form->isSubmitted() && $form->isValid()) {
      $this->messageService->create($this->getUser(), $trick, $message);
      $this->addFlash('succes', 'Votre commentaire a bien été ajouté!');
      
      return $this->redirectToRoute('trick_display_one', [
        'id' => $trick->getId()
      ]);
    }

    $category['name'] = $trick->getCategory()->getName();
    
    return $this->render('trick/displayone.html.twig', [
      'trick' => $trick,
      'messages' => $messagesArray,
      'createMessageForm' => $form->createView(),
      'images' => $images,
      'videos' => $videos,
      'imagePath' => $imagePath,
      'category' => $category
    ]);
  }

  // CREATE
  #[Route('/trick/create', name: 'trick_create')]
  public function create(Request $request): Response
  {
    $trick = new Trick();
    $form = $this->createForm(CreationTrick::class, $trick);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
      $imageFile = $form->get('image')->getData();
      $videos = $form->get('videos')->getData();
      if($videos === null){
        $videos = '';
      }
      $this->trickService->create($this->getUser(), $trick, $imageFile, $videos);
      $this->addFlash('succes', 'Votre trick a bien été ajouté!');

      return $this->redirectToRoute('home', [
        '_fragment' => 'tricks-container'
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
      $imageFiles = $form->get('image')->getData();
      $videos = $form->get('videos')->getData();
      if($videos === null){
        $videos = '';
      }
      $trick = $this->trickService->update($trick, $imageFiles, $videos);
      $this->addFlash('succes', 'Le trick a bien été modifié.');

      return $this->redirectToRoute('trick_display_one', [
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
  #[Route('/trick/delete/{id}', name: 'trick_delete')]
  public function delete(int $id): Response
  {
    if($this->getUser()){
      $this->trickService->delete($id);
      $this->addFlash('succes', 'Le trick a bien été supprimé.');
      
      return $this->redirectToRoute('home');
    }
    
    return $this->redirectToRoute('home');
  }
}
