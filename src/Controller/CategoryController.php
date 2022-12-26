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

use App\Form\CreationCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

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

  #[Route('/category/showone/{id}/{succesMessage}', name: 'category_show')]
  public function show(int $id, string $succesMessage = null): Response
  {
    $category = $this->categoryService->getOne($id);
    
    return $this->render('category/showone.html.twig', [
      'category' => $category,
      'succesMessage' => $succesMessage
    ]);
  }

  #[Route('/category/create', name: 'create_category')]
  public function createCategory(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
  {
    $category = new Category();
    $form = $this->createForm(CreationCategory::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->updated_at = new DateTimeImmutable();
      $category
        ->setUser($user)
        ->setUpdatedAt($this->updated_at)
      ;
      $entityManager->persist($category);
      $entityManager->flush();
      // do anything else you need here, like send an email

      // Ici je dois retourner la page du trick que je viens de créer
      $succesMessage = 'Votre catégorie a bien été ajoutée!';
      return $this->redirectToRoute('category_show', [
        'id' => $category->getId(),
        'succesMessage' => $succesMessage
      ]);
    }

    return $this->render('category/creationPage.html.twig', [
      'controller_name' => 'CategoryController',
      'registrationForm' => $form->createView()
    ]);
  }

  /*#[Route('/category/creationpage', name: 'create_category_page')]
  public function createCategoryPage(): Response
  {
    return $this->render('category/creationPage.html.twig', [
      'controller_name' => 'CategoryController',
    ]);
  }

  #[Route('/category/create', name: 'create_category')]
  public function createProduct(): Response
  {
    $category = $this->categoryService->create();

    return $this->render('category/showone.html.twig', [
      'category' => $category
    ]);
  }*/

  #[Route('/category/update/{id}', name: 'update_category')]
  public function updateCategory(int $id, Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
  {
    $category = new Category();
    $form = $this->createForm(CreationCategory::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->updated_at = new DateTimeImmutable();
      $category
        ->setUser($user)
        ->setUpdatedAt($this->updated_at)
      ;
      $test = $this->categoryService->update($id);
      dd($test);
      // do anything else you need here, like send an email

      // Ici je dois retourner la page du trick que je viens de créer
      $succesMessage = 'Votre catégorie a bien été ajoutée!';
      return $this->redirectToRoute('category_show', [
        'id' => $category->getId(),
        'succesMessage' => $succesMessage
      ]);
    }

    $category = $this->categoryService->updatePage($id);

    return $this->render('category/updatePage.html.twig', [
      'category' => $category,
      'registrationForm' => $form->createView()
    ]);
  }

  /*#[Route('/category/updatepage/{id}', name: 'update_category_page')]
  public function updateCategoryPage(ManagerRegistry $doctrine, int $id): Response
  {
    $category = $this->categoryService->updatePage($id);

    return $this->render('category/updatePage.html.twig', [
      'category' => $category,
    ]);
  }

  #[Route('/category/update/{id}', name: 'update_category')]
  public function updateCategory(int $id): Response
  {
    $category = $this->categoryService->update($id);

    return new Response('Saved new category with id ' . $category->getId());
  }*/

  #[Route('/category/delete/{id}', name: 'delete_category')]
  public function deleteCategory(int $id): Response
  {
    $this->categoryService->delete($id);

    return new Response('Catégorie supprimée ! ');
  }
}
