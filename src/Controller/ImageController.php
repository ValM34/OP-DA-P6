<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ImageServiceInterface;
use App\Entity\Image;

class ImageController extends AbstractController
{
  public function __construct(private ImageServiceInterface $imageService)
  {}
  
  // DELETE
  #[Route('/image/delete/{slug}', name: 'image_delete', methods: ['GET'])]
  public function delete(Image $image): Response
  {
    if ($this->getUser()) {
      $trickSlug = $this->imageService->delete($image);
      $this->addFlash('succes', "L'image a bien été supprimée.");

      return $this->redirectToRoute('trick_display_one', [
        'slug' => $trickSlug
      ]);
    }

    return $this->redirectToRoute('trick_display_one');
  }
}
