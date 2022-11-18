<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Message;
// use App\Service\categoryService;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\CategoryServiceInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
  use CreatedAtTrait;

  private $categoryService;

  public function __construct(CategoryServiceInterface $categoryService)
  {
    $this->trick = new Trick();
    $this->trick = new User();
    $this->trick = new Message();
    $this->categoryService = $categoryService;
  }

  #[Route('/category/all', name: 'category_show_all')]
  public function showAll(): Response
  {
    $categories = $this->categoryService->findAll();

    return $this->render('category/index.html.twig', [
      'categories' => $categories
    ]);
  }

  #[Route('/trick/showone/{id}', name: 'trick_show')]
  public function show(ManagerRegistry $doctrine, int $id): Response
  {
    $trick = $this->categoryService->getOne($id);
    
    return $this->render('trick/showone.html.twig', [
      'trick' => $trick
    ]);
  }

  #[Route('/trick/creationpage', name: 'create_trick_page')]
  public function createProductPage(): Response
  {
    return $this->render('trick/creationPage.html.twig', [
      'controller_name' => 'TrickController',
    ]);
  }

  #[Route('/trick/create', name: 'create_trick')]
  public function createProduct(): Response
  {
    $trick = $this->categoryService->create();

    return new Response('Saved new product with id ' . $trick->getId());
  }

  #[Route('/trick/updatepage/{id}', name: 'update_trick_page')]
  public function updateTrickPage(ManagerRegistry $doctrine, int $id): Response
  {
    $trick = $this->categoryService->updatePage($id);

    return $this->render('trick/updatePage.html.twig', [
      'trick' => $trick,
    ]);
  }

  #[Route('/trick/update/{id}', name: 'update_trick')]
  public function updateTrick(int $id): Response
  {
    $trick = $this->categoryService->update($id);

    return new Response('Saved new product with id ' . $trick->getId());
  }

  #[Route('/trick/delete/{id}', name: 'delete_trick')]
  public function deleteTrick(int $id): Response
  {
    $this->categoryService->delete($id);

    return new Response('Trick supprimé ! ');
  }
}
