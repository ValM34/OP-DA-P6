<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Message;
// use App\Service\TrickService;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\TrickServiceInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
  use CreatedAtTrait;

  private $trickService;

  public function __construct(TrickServiceInterface $trickService)
  {
    $this->trick = new Trick();
    $this->trick = new User();
    $this->trick = new Message();
    $this->trick = new Category();
    $this->trickService = $trickService;
  }

  #[Route('/trick/all', name: 'trick_show_all')]
  public function showAll(): Response
  {
    $tricks = $this->trickService->findAll();

    return $this->render('trick/index.html.twig', [
      'controller_name' => 'TrickController',
      'tricks' => $tricks
    ]);
  }

  #[Route('/trick/showone/{id}', name: 'trick_show')]
  public function show(ManagerRegistry $doctrine, int $id): Response
  {
    $trick = $this->trickService->getOne($id);
    $messages = $this->trickService->getMessages($id);
    
    return $this->render('trick/showone.html.twig', [
      'trick' => $trick,
      'messages' => $messages
    ]);
  }

  #[Route('/trick/creationpage', name: 'create_trick_page')]
  public function createProductPage(): Response
  {
    $categories = $this->trickService->findAllCategories();

    return $this->render('trick/creationPage.html.twig', [
      'controller_name' => 'TrickController',
      'categories' => $categories
    ]);
  }

  #[Route('/trick/create', name: 'create_trick')]
  public function createProduct(): Response
  {
    $trick = $this->trickService->create();

    return new Response('Saved new product with id ' . $trick->getId());
  }

  #[Route('/trick/updatepage/{id}', name: 'update_trick_page')]
  public function updateTrickPage(ManagerRegistry $doctrine, int $id): Response
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

    return new Response('Saved new product with id ' . $trick->getId());
  }

  #[Route('/trick/delete/{id}', name: 'delete_trick')]
  public function deleteTrick(int $id): Response
  {
    $this->trickService->delete($id);

    return new Response('Trick supprimé ! ');
  }
}
