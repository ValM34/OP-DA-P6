<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image;
use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageService implements ImageServiceInterface
{
  private $entityManager;
  private $targetDirectory;
  private $slugger;
  
  public function __construct(EntityManagerInterface $entityManager, $targetDirectory, SluggerInterface $slugger)
  {
    $this->entityManager = $entityManager;
    $this->targetDirectory = $targetDirectory;
    $this->slugger = $slugger;
    $this->request = new Request(
      $_GET,
      $_POST,
      [],
      $_COOKIE,
      $_FILES,
      $_SERVER
    );
  }

  // @TODO commentaires des méthodes
  public function findAll()
  {
    dd($this->entityManager->getRepository(Image::class)->findAll()[0]->setTrick());
    return $this->entityManager->getRepository(Image::class)->findAll();
  }

  public function create(Trick $trick, $arrayOfImages)
  {
    $date = new DateTimeImmutable();
    
    for($i = 0; $i < count($arrayOfImages); $i++){
      $image = new Image();
      $image
        ->setPath($arrayOfImages[$i])
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
        ->setTrick($trick)
      ;
    $this->entityManager->persist($image);
    }

    $this->entityManager->flush();
  }

  public function upload(UploadedFile $file)
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

  public function getTargetDirectory()
  {
    return $this->targetDirectory;
  }

  public function delete(int $id)
  {
    $image = $this->entityManager->getRepository(Image::class)->find($id);
    $idTrick = $image->getTrick()->getId();
    $imageWholePath = $this->getTargetDirectory() . '/' . $image->getPath();

    if(file_exists($imageWholePath)){
      unlink($imageWholePath);
    }
    $this->entityManager->remove($image);
    $this->entityManager->flush();

    return $idTrick;
  }

  public function deleteByTrick(array $images)
  {
    foreach($images as $image){
      $imageWholePath = $this->getTargetDirectory() . '/' . $image->getPath();
      if(file_exists($imageWholePath)){
        unlink($imageWholePath);
      }
    }
  }
}
