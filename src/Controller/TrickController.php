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
use App\Form\MessageForm;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class TrickController extends AbstractController
{
  use CreatedAtTrait;

  private $message;

  public function __construct(
    private TrickServiceInterface $trickService,
    private MessageServiceInterface $messageService,
    private ImageServiceInterface $imageService,
    private UserServiceInterface $userService,
    private CategoryServiceInterface $categoryService
  ) {
    $this->message = new Message();
  }

  // DISPLAY ALL
  #[Route('/', name: 'home', methods: ['GET'])]
  public function displayAll(): Response
  {
    $tricks = $this->trickService->findAll();

    return $this->render('home/home.html.twig', [
      'tricks' => $tricks
    ]);
  }
  
  // public function displayOne(Request $request, #[MapEntity(expr: 'repository.findTrick(video)')] Trick $trick): Response
  // DISPLAY ONE
  #[Route('/trick/displayone/{slug}', name: 'trick_display_one', methods: ['GET', 'POST'])]
  #[Entity('trick', expr: 'repository.findTrick(slug)')]
  public function displayOne(Request $request, Trick $trick): Response
  {
    $form = $this->createForm(MessageForm::class, $this->message);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->messageService->create($this->getUser(), $trick, $this->message);
      $this->addFlash('succes', 'Votre commentaire a bien été ajouté!');

      return $this->redirectToRoute('trick_display_one', [
        'slug' => $trick->getSlug()
      ]);
    }

    return $this->render('trick/displayOne.html.twig', [
      'trick' => $trick,
      'messageForm' => $form->createView()
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
      $trick = $this->trickService->create($this->getUser(), $trick, $form);
      $this->addFlash('succes', 'Votre trick a bien été ajouté!');

      return $this->redirectToRoute('trick_display_one', [
        'slug' => $trick->getSlug()
      ]);
    }

    return $this->render('trick/create.html.twig', [
      'categories' => $this->categoryService->findAll(),
      'createTrickForm' => $form->createView()
    ]);
  }

  // UPDATE
  #[Route('/trick/update/{slug}', name: 'trick_update', methods: ['GET', 'POST'])]
  public function update(Request $request, Trick $trick): Response
  {
    $form = $this->createForm(TrickForm::class, $trick);
    $form->handleRequest($request);

    if (($form->isSubmitted() && $form->isValid() && $this->getUser())) {
      $this->trickService->update($trick, $form);
      $this->addFlash('succes', 'Le trick a bien été modifié.');

      return $this->redirectToRoute('trick_display_one', [
        'slug' => $trick->getSlug()
      ]);
    }

    return $this->render('trick/update.html.twig', [
      'trick' => $trick,
      'updateTrickForm' => $form->createView()
    ]);
  }

  // DELETE
  #[Route('/trick/delete/{slug}', name: 'trick_delete', methods: ['GET'])]
  public function delete(Trick $trick): Response
  {
    if ($this->getUser()) {
      $this->trickService->delete($trick);
      $this->addFlash('succes', 'Le trick a bien été supprimé.');
    }

    return $this->redirectToRoute('home');
  }
}
