<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\VideoServiceInterface;
use App\Entity\Video;

class VideoController extends AbstractController
{
  public function __construct(private VideoServiceInterface $videoService)
  {}

  #[Route('/video/delete/{slug}', name: 'video_delete', methods: ['GET'])]
  public function delete(Video $video): Response
  {
    if ($this->getUser()) {
      $slugTrick = $this->videoService->delete($video);
      if($slugTrick === null){
        return $this->redirectToRoute('home');
      }
      $this->addFlash('succes', 'La video a bien été supprimé.');

      return $this->redirectToRoute('trick_display_one', [
        'slug' => $slugTrick
      ]);
    }
    $this->addFlash('error', "Action non autorisée.");

    return $this->redirectToRoute('home');
  }
}
