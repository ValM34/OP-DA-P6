<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Category;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request; 
use App\Service\ImageServiceInterface;
use App\Service\VideoServiceInterface;

class TrickService implements TrickServiceInterface
{
  private $entityManager;
  private $imageService;
  private $videoService;

  public function __construct(EntityManagerInterface $entityManager, ImageServiceInterface $imageService, VideoServiceInterface $videoService)
  {
    $this->entityManager = $entityManager;
    $this->imageService = $imageService;
    $this->videoService = $videoService;
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
  public function findAll()
  {
    return $this->entityManager->getRepository(Trick::class)->findAll();
  }

  public function findOne(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);
    if (!$trick) {
      $trick = null;
    }

    return $trick;
  }

  // CREATE
  public function create($user, Trick $trick, array $imageFiles, string $videos)
  {
    $date = new DateTimeImmutable();
    $trick
      ->setUser($user)
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
    ;

    $this->entityManager->persist($trick);
    $this->entityManager->flush();

    if (isset($imageFiles[0])) {
      $arrayOfImages = [];
      foreach($imageFiles as $imageFile){
        $arrayOfImages[] = $this->imageService->upload($imageFile);
      }

      $this->entityManager->persist($trick);
      $this->entityManager->flush();

      $this->imageService->create($trick, $arrayOfImages);

    } else {
      $this->entityManager->persist($trick);
      $this->entityManager->flush();
    }

    $videos = str_replace(' ', '', $videos);
    $separators = ", ;";
    $videosArray = preg_split("/[" . $separators . "]/", $videos);
    foreach($videosArray as $video){
      if (strpos($video, 'https://www.youtube.com') !== false || strpos($video, 'https://vimeo.com') !== false) {
        dump("La chaîne contient l'URL de YouTube ou l'URL de Vimeo");
      } else {
        dump("La chaîne ne contient pas l'URL de YouTube ni l'URL de Vimeo");
        $videosArray = array_diff($videosArray, [$video]);
      }
    }
    $videosArray = array_values($videosArray);
    $videosArray = str_replace('https://www.youtube.com/watch?v=', 'https://www.youtube.com/embed/', $videosArray);
    $videosArray = str_replace('https://vimeo.com/', 'https://player.vimeo.com/video/', $videosArray);
    $this->videoService->create($trick, $videosArray);
  }

  // UPDATE PAGE
  public function updatePage(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);

    if (!$trick) {
      $trick = null;
    }

    return $trick;
  }

  // UPDATE
  public function update(Trick $trick, array $imageFiles, string $videos)
  {
    $date = new DateTimeImmutable();
    $trick
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
    ;

    if (isset($imageFiles[0])) {
      $arrayOfImages = [];
      foreach($imageFiles as $imageFile){
        $arrayOfImages[] = $this->imageService->upload($imageFile);
      }
      $this->entityManager->persist($trick);
      $this->entityManager->flush();
      $this->imageService->create($trick, $arrayOfImages);
    } else {
      $this->entityManager->persist($trick);
      $this->entityManager->flush();
    }

    $videos = str_replace(' ', '', $videos);
    $separators = ", ;";
    $videosArray = preg_split("/[" . $separators . "]/", $videos);
    foreach($videosArray as $video){
      if (strpos($video, 'https://www.youtube.com') !== false || strpos($video, 'https://vimeo.com') !== false) {
        dump("La chaîne contient l'URL de YouTube ou l'URL de Vimeo");
      } else {
        dump("La chaîne ne contient pas l'URL de YouTube ni l'URL de Vimeo");
        $videosArray = array_diff($videosArray, [$video]);
      }
    }
    $videosArray = array_values($videosArray);
    $videosArray = str_replace('https://www.youtube.com/watch?v=', 'https://www.youtube.com/embed/', $videosArray);
    $videosArray = str_replace('https://vimeo.com/', 'https://player.vimeo.com/video/', $videosArray);
    $this->videoService->create($trick, $videosArray);
  }

  // DELETE
  public function delete(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);
    $images = $this->entityManager->getRepository(Image::class)->findByTrick($trick);
    $this->imageService->deleteByTrick($images);
    $this->entityManager->remove($trick);
    $this->entityManager->flush();
  }

  // FIND ALL CATEGORIES
  public function findAllCategories()
  {
    return $this->entityManager->getRepository(Category::class)->findAll();
  }
}
