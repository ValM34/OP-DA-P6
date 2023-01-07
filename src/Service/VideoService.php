<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Video;
use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class VideoService implements VideoServiceInterface
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
    $this->request = new Request(
      $_GET,
      $_POST,
      [],
      $_COOKIE,
      $_FILES,
      $_SERVER
    );
  }

  public function create(Trick $trick, $arrayOfVideos)
  {
    $date = new DateTimeImmutable();
    foreach($arrayOfVideos as $videoUpdated){
      $vdo = new Video();
      $vdo
        ->setPath($videoUpdated)
        ->setTrick($trick)
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
      ;
      dump($vdo);
      $this->entityManager->persist($vdo);
    }
    $this->entityManager->flush();
  }

  public function delete(int $id)
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
