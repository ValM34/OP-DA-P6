<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Video;
use App\Entity\Trick;

class VideoService implements VideoServiceInterface
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function create(Trick $trick, array $arrayOfVideos): void
  {
    $date = new DateTimeImmutable();
    foreach($arrayOfVideos as $videoUpdated){
      $vdo = new Video();
      $vdo
        ->setPath($videoUpdated)
        ->setTrick($trick)
        ->setCreatedAt($date)
      ;
      $this->entityManager->persist($vdo);
    }
    $this->entityManager->flush();
  }

  public function delete(int $id): ?int
  {
    $video = $this->entityManager->getRepository(Video::class)->find($id);
    if($video === null){
      return $idTrick = null;
    }
    $idTrick = $video->getTrick()->getId();
    $this->entityManager->remove($video);
    $this->entityManager->flush();

    return $idTrick;
  }
}
