<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Message;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\TrickServiceInterface;
use App\Service\ImageServiceInterface;
use App\Service\UserServiceInterface;
use App\Service\MessageServiceInterface;
use App\Service\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrickForm;
use App\Form\MessageCreateType;
use Symfony\Component\HttpFoundation\Request;

class TrickController extends AbstractController
{
  use CreatedAtTrait;

  private $trickService;
  private $imageService;
  private $messageService;
  private $userService;
  private $categoryService;
  private $message;

  public function __construct(
    TrickServiceInterface $trickService,
    MessageServiceInterface $messageService,
    ImageServiceInterface $imageService,
    UserServiceInterface $userService,
    CategoryServiceInterface $categoryService
  ) {
    $this->trickService = $trickService;
    $this->imageService = $imageService;
    $this->userService = $userService;
    $this->messageService = $messageService;
    $this->categoryService = $categoryService;
    $this->message = new Message();
  }

  // DISPLAY ALL
  #[Route('/', name: 'home', methods: ['GET'])]
  public function displayAll(): Response
  {
    $tricks = $this->trickService->findAll();
    $tricksData = [];
    
    for ($i = 0, $count = count($tricks); $i < $count; $i++) {
      $tricksData[$i]['id'] = $tricks[$i]->getId();
      $tricksData[$i]['name'] = $tricks[$i]->getName();
      if ($tricks[$i]->getImages()[0] !== null) {
        $tricksData[$i]['path'] = 'images/tricks/' . $tricks[$i]->getImages()[0]->getPath();
      } else {
        $tricksData[$i]['path'] = 'images/TrickDefault.jpg';
      }
    }

    return $this->render('home/home.html.twig', [
      'tricks' => $tricksData
    ]);
  }

  // DISPLAY ONE
  #[Route('/trick/displayone/{id}', name: 'trick_display_one', methods: ['GET', 'POST'])]
  public function displayOne(int $id, Request $request): Response
  {
    $form = $this->createForm(MessageCreateType::class, $this->message);
    $form->handleRequest($request);
    $trick = $this->trickService->findOne($id);
    $messages = $this->messageService->findByTrick($id);
    if ($this->getUser()) {
      $actualUser = $this->userService->findOne($this->getUser())->getId();
    } else {
      $actualUser = null;
    }
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
      if ($this->getUser()) {
        if ($messages[$i]->getUser()->getId() === $actualUser) {
          $arr = array_merge($arr, ['isOwner' => true]);
        } else {
          $arr = array_merge($arr, ['isOwner' => false]);
        }
      } else {
        $arr = array_merge($arr, ['isOwner' => false]);
      }
      array_push($messagesArray, $arr);
    }

    $images = [];
    $imagePath = null;

    if ($trick->getImages()[0] !== null) {
      $imagePath = $trick->getImages()[0]->getPath();
      for ($i = 0, $count = count($trick->getImages()); $i < $count; $i++) {
        $images[$i]['path'] = $trick->getImages()[$i]->getPath();
        $images[$i]['id'] = $trick->getImages()[$i]->getId();
      }
    } else {
      $imagePath = '../trickDefault.jpg';
    }

    $videos = [];
    if ($trick->getVideos()[0] !== null) {
      for ($i = 0, $count = count($trick->getVideos()); $i < $count; $i++) {
        $videos[$i]['path'] = $trick->getVideos()[$i]->getPath();
        $videos[$i]['id'] = $trick->getVideos()[$i]->getId();
      }
    }

    if ($form->isSubmitted() && $form->isValid()) {
      $this->messageService->create($this->getUser(), $trick, $this->message);
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
  #[Route('/trick/create', name: 'trick_create', methods: ['GET', 'POST'])]
  public function create(Request $request): Response
  {
    $trick = new Trick();
    $form = $this->createForm(TrickForm::class, $trick);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
      $imageFile = $form->get('image')->getData();
      $videos = $form->get('videos')->getData();
      if ($videos === null) {
        $videos = '';
      }
      $this->trickService->create($this->getUser(), $trick, $imageFile, $videos);
      $this->addFlash('succes', 'Votre trick a bien été ajouté!');

      return $this->redirectToRoute('home', [
        '_fragment' => 'tricks-container'
      ]);
    }

    return $this->render('trick/create.html.twig', [
      'categories' => $this->categoryService->findAll(),
      'createTrickForm' => $form->createView()
    ]);
  }

  // UPDATE
  #[Route('/trick/update/{id}', name: 'trick_update', methods: ['GET', 'POST'])]
  public function update(int $id, Request $request): Response
  {
    $trick = $this->trickService->findOne($id);
    $form = $this->createForm(TrickForm::class, $trick);
    $form->handleRequest($request);

    if (($form->isSubmitted() && $form->isValid() && $this->getUser())) {
      $imageFiles = $form->get('image')->getData();
      $videos = $form->get('videos')->getData();
      if ($videos === null) {
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
  #[Route('/trick/delete/{id}', name: 'trick_delete', methods: ['GET'])]
  public function delete(int $id): Response
  {
    if ($this->getUser()) {
      $this->trickService->delete($id);
      $this->addFlash('succes', 'Le trick a bien été supprimé.');
    }

    return $this->redirectToRoute('home');
  }
}
