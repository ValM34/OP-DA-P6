<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageService implements ImageServiceInterface
{
  private $entityManager;
  private $targetDirectory;
  private $slugger;
  
  public function __construct(EntityManagerInterface $entityManager, string $targetDirectory, SluggerInterface $slugger)
  {
    $this->entityManager = $entityManager;
    $this->targetDirectory = $targetDirectory;
    $this->slugger = $slugger;
  }

  // CREATE
  public function create(Trick $trick, array $arrayOfImages): void
  {
    $date = new DateTimeImmutable();
    
    for($i = 0, $count = count($arrayOfImages); $i < $count; $i++){
      $image = new Image();
      $image
        ->setPath($arrayOfImages[$i])
        ->setCreatedAt($date)
        ->setTrick($trick)
      ;
    $this->entityManager->persist($image);
    }

    // $this->entityManager->flush();
  }

  // UPLOAD
  public function upload(UploadedFile $file): string
  {
    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    // this is needed to safely include the file name as part of the URL
    $safeFilename = $this->slugger->slug($originalFilename);
    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

    // Move the file to the directory where images are stored
    try {
      $file->move($this->getTargetDirectory(), $newFilename);
    } catch (FileException $e) {
      // ... handle exception if something happens during file upload @TODO Faire quelque chose pour pas que le site renvoie une erreur
    }

    return $newFilename;
  }

  // GET TARGET DIRECTORY
  public function getTargetDirectory(): string
  {
    return $this->targetDirectory;
  }

  // DELETE
  public function delete(Image $image): string
  {
    $slug = $image->getTrick()->getSlug();
    $imageWholePath = $this->getTargetDirectory() . '/' . $image->getPath();

    if(file_exists($imageWholePath)){
      unlink($imageWholePath);
    }
    $this->entityManager->remove($image);
    $this->entityManager->flush();

    return $slug;
  }

  // DELETE BY TRICK
  public function deleteByTrick(array $images): void
  {
    foreach($images as $image){
      $imageWholePath = $this->getTargetDirectory() . '/' . $image->getPath();
      if(file_exists($imageWholePath)){
        unlink($imageWholePath);
      }
    }
  }
}
