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
use App\Form\CategoryForm;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
  use CreatedAtTrait;

  private $category;
  private $dateTimeImmutable;

  public function __construct(private CategoryServiceInterface $categoryService, private UserServiceInterface $userService)
  {
    $this->category = new Category();
    $this->dateTimeImmutable = new DateTimeImmutable();
  }

  // DISPLAY ALL
  #[Route('/category/displayall', name: 'category_display_all', methods: ['GET'])]
  public function displayAll(): Response
  {
    $categories = $this->categoryService->findAll();

    return $this->render('category/displayAll.html.twig', [
      'categories' => $categories
    ]);
  }

  // DISPLAY ONE
  #[Route('/category/displayone/{slug}', name: 'category_display_one', methods: ['GET'])]
  public function displayOne(Category $category): Response
  {
    return $this->render('category/displayOne.html.twig', [
      'category' => $category
    ]);
  }

  // CREATE
  #[Route('/category/create', name: 'category_create', methods: ['GET', 'POST'])]
  public function createCategory(Request $request): Response
  {
    $form = $this->createForm(CategoryForm::class, $this->category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
      $this->categoryService->create($this->getUser(), $this->category);
      $this->addFlash('succes', 'Votre catégorie a bien été ajoutée!');
      
      return $this->redirectToRoute('category_display_one', [
        'slug' => $this->category->getSlug()
      ]);
    }

    return $this->render('category/creationPage.html.twig', [
      'categoryForm' => $form->createView()
    ]);
  }

  // UPDATE
  #[Route('/category/update/{slug}', name: 'category_update', methods: ['GET', 'POST'])]
  public function update(Category $category, Request $request): Response
  {
    $form = $this->createForm(CategoryForm::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->categoryService->update($category);
      $this->addFlash('succes', 'La catégorie a été modifiée.');

      return $this->redirectToRoute('category_display_one', [
        'slug' => $category->getSlug()
      ]);
    }

    return $this->render('category/update.html.twig', [
      'category' => $category,
      'categoryForm' => $form->createView()
    ]);
  }

  // DELETE
  #[Route('/category/delete/{slug}', name: 'category_delete', methods: ['GET'])]
  public function deleteCategory(Category $category): Response
  {
    $this->categoryService->delete($category);
    $this->addFlash('succes', 'La catégorie a bien été supprimée.');

    return $this->redirectToRoute('category_display_all');
  }
}
