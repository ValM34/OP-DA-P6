<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
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
    $this->trickService = $trickService;
  }

  #[Route('/trick', name: 'app_trick')]
  public function index(): Response
  {
    var_dump($this->trick->getId());
    return $this->render('trick/index.html.twig', [
      'controller_name' => 'TrickController',
    ]);
  }

  #[Route('/trick/all', name: 'trick_show_all')]
  public function showAll(ManagerRegistry $doctrine): Response
  {
    $trick = $doctrine->getRepository(Trick::class)->findAll();
    if (!$trick) {
      throw $this->createNotFoundException(
        'No product found'
      );
    }
    // dd($trick);

    // return new Response('Check out this great product: ' . $trick);
    return $this->render('trick/index.html.twig', [
      'controller_name' => 'TrickController', 'allTricks' => $trick
    ]);
    // or render a template
    // in the template, print things with {{ product.name }}
    // return $this->render('product/show.html.twig', ['product' => $product]);
  }

  #[Route('/trick/{id}', name: 'trick_show')]
  public function show(ManagerRegistry $doctrine, int $id): Response
  {
    $trick = $doctrine->getRepository(Trick::class)->find($id,);
    if (!$trick) {
      throw $this->createNotFoundException(
        'No product found for id ' . $id
      );
    }

    return new Response('Check out this great product: ' . $trick->getName() . $trick->getId());

    // or render a template
    // in the template, print things with {{ product.name }}
    // return $this->render('product/show.html.twig', ['product' => $product]);
  }

  #[Route('/trick/create', name: 'create_trick')]
  public function createProduct(): Response
  {
    $trick = $this->trickService->create();

    return new Response('Saved new product with id ' . $trick->getId());
  }
}
