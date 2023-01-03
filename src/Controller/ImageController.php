<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ImageServiceInterface;

class ImageController extends AbstractController
{
  private $imageService;

  public function __construct(ImageServiceInterface $imageService)
  {
    $this->imageService = $imageService;
  }
  #[Route('/image/delete/{id}', name: 'image_delete')]
  public function delete(int $id): Response
  {
    if ($this->getUser()) {
      $idTrick = $this->imageService->delete($id);

      return $this->redirectToRoute('trick_display_one', [
        'id' => $idTrick,
        'succesMessage' => "L'image a bien été supprimé."
      ]);
    }

    return $this->redirectToRoute('trick_display_one');
  }
}
