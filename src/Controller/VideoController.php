<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\VideoServiceInterface;

class VideoController extends AbstractController
{
  private $videoService;

  public function __construct(VideoServiceInterface $videoService)
  {
    $this->videoService = $videoService;
  }

  #[Route('/video/delete/{id}', name: 'video_delete', methods: ['GET'])]
  public function delete(int $id): Response
  {
    if ($this->getUser()) {
      $idTrick = $this->videoService->delete($id);
      if($idTrick === null){
        return $this->redirectToRoute('home');
      }
      $this->addFlash('succes', 'La video a bien été supprimé.');

      return $this->redirectToRoute('trick_display_one', [
        'id' => $idTrick
      ]);
    }
    $this->addFlash('error', "Action non autorisée.");

    return $this->redirectToRoute('trick_display_one');
  }
}
