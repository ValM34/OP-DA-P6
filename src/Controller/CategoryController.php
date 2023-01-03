<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Trait\CreatedAtTrait;
use App\Service\CategoryServiceInterface;
use App\Service\UserServiceInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CreationCategory;
use App\Form\UpdateCategory;
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
    $this->request = new Request(
      $_GET,
      $_POST,
      [],
      $_COOKIE,
      $_FILES,
      $_SERVER
    );
  }

  // DISPLAY ALL
  #[Route('/category/displayall/{succesMessage}', name: 'category_display_all')]
  public function displayAll($succesMessage = null): Response
  {
    $categories = $this->categoryService->findAll();

    return $this->render('category/displayAll.html.twig', [
      'categories' => $categories,
      'succesMessage' => $succesMessage
    ]);
  }

  // DISPLAY ONE
  #[Route('/category/displayone/{id}/{succesMessage}', name: 'category_display_one')]
  public function displayOne(int $id, string $succesMessage = null): Response
  {
    $category = $this->categoryService->findOne($id);
    
    return $this->render('category/displayOne.html.twig', [
      'category' => $category,
      'succesMessage' => $succesMessage
    ]);
  }

  // CREATE
  #[Route('/category/create', name: 'category_create')]
  public function createCategory(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
  {
    $category = new Category();
    $form = $this->createForm(CreationCategory::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
      $this->categoryService->create($this->getUser(), $category);
      $succesMessage = 'Votre catégorie a bien été ajoutée!';
      
      return $this->redirectToRoute('category_display_one', [
        'id' => $category->getId(),
        'succesMessage' => $succesMessage
      ]);
    }

    return $this->render('category/creationPage.html.twig', [
      'CreationCategoryForm' => $form->createView()
    ]);
  }

  // UPDATE
  #[Route('/category/update/{id}', name: 'category_update')] // changer nom route @TODO
  public function update(int $id, Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
  {
    $category = $this->categoryService->findOne($id);
    $form = $this->createForm(UpdateCategory::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->updated_at = new DateTimeImmutable();
      $category
        ->setUser($user)
        ->setUpdatedAt($this->updated_at)
      ;
      $this->categoryService->update($id);

      $succesMessage = 'La catégorie a été modifiée.';
      return $this->redirectToRoute('category_display_one', [
        'id' => $category->getId(),
        'succesMessage' => $succesMessage
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
    $succesMessage = 'La catégorie a bien été supprimée.';

    return $this->redirectToRoute('category_display_all', [
      'succesMessage' => $succesMessage
    ]);
  }
}
