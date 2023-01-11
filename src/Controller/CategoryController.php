<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Category;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\CategoryServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CreationCategory;
use App\Form\UpdateCategoryForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryController extends AbstractController
{
  use CreatedAtTrait;

  private $categoryService;
  private $userService;

  public function __construct(CategoryServiceInterface $categoryService, UserServiceInterface $userService)
  {
    $this->categoryService = $categoryService;
    $this->userService = $userService;
  }

  // DISPLAY ALL
  #[Route('/category/displayall', name: 'category_display_all')]
  public function displayAll(): Response
  {
    $categories = $this->categoryService->findAll();

    return $this->render('category/displayAll.html.twig', [
      'categories' => $categories
    ]);
  }

  // DISPLAY ONE
  #[Route('/category/displayone/{id}', name: 'category_display_one')]
  public function displayOne(int $id): Response
  {
    $category = $this->categoryService->findOne($id);
    
    return $this->render('category/displayOne.html.twig', [
      'category' => $category
    ]);
  }

  // CREATE
  #[Route('/category/create', name: 'category_create')]
  public function createCategory(Request $request): Response
  {
    $category = new Category();
    $form = $this->createForm(CreationCategory::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
      $this->categoryService->create($this->getUser(), $category);
      $this->addFlash('succes', 'Votre catégorie a bien été ajoutée!');
      
      return $this->redirectToRoute('category_display_one', [
        'id' => $category->getId()
      ]);
    }

    return $this->render('category/creationPage.html.twig', [
      'CreationCategoryForm' => $form->createView()
    ]);
  }

  // UPDATE
  #[Route('/category/update/{id}', name: 'category_update')]
  public function update(int $id, Request $request, UserInterface $user): Response
  {
    $category = $this->categoryService->findOne($id);
    $form = $this->createForm(UpdateCategoryForm::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $updated_at = new DateTimeImmutable();
      $category
        ->setUser($user)
        ->setUpdatedAt($updated_at)
      ;
      $this->categoryService->update($id);

      $this->addFlash('succes', 'La catégorie a été modifiée.');
      return $this->redirectToRoute('category_display_one', [
        'id' => $category->getId()
      ]);
    }

    $category = $this->categoryService->updatePage($id);

    return $this->render('category/update.html.twig', [
      'category' => $category,
      'updateCategoryForm' => $form->createView()
    ]);
  }

  // DELETE
  #[Route('/category/delete/{id}', name: 'category_delete')]
  public function deleteCategory(int $id): Response
  {
    $this->categoryService->delete($id);
    $this->addFlash('succes', 'La catégorie a bien été supprimée.');

    return $this->redirectToRoute('category_display_all');
  }
}
