<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Category;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request; 
use App\Service\ImageServiceInterface;

class TrickService implements TrickServiceInterface
{
  private $entityManager;
  private $imageService;

  public function __construct(EntityManagerInterface $entityManager, ImageServiceInterface $imageService)
  {
    $this->entityManager = $entityManager;
    $this->imageService = $imageService;
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


















  public function create($user, Trick $trick, array $imageFiles)
  {
    $date = new DateTimeImmutable();
    $trick
      ->setUser($user)
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
    ;

    $this->entityManager->persist($trick);

    // $this->imageService->create($trick, $arrayOfImages); @TODO gérer l'ajout d'images ici (puis les vidéos aussi)

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
  }

  

  public function updatePage(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);

    if (!$trick) {
      $trick = null;
    }

    return $trick;
  }

  public function update(Trick $trick, array $imageFiles)
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
  }

  public function delete(int $id)
  {
    $trick = $this->entityManager->getRepository(Trick::class)->find($id);
    $images = $this->entityManager->getRepository(Image::class)->findByTrick($trick);
    $this->imageService->deleteByTrick($images);
    $this->entityManager->remove($trick);
    $this->entityManager->flush();
  }





  public function findAllCategories()
  {
    return $this->entityManager->getRepository(Category::class)->findAll();
  }
}
