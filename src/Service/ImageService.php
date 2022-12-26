<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image;
use App\Entity\Category;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request; 

class ImageService implements ImageServiceInterface
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

public function findAll()
  {
    dd($this->entityManager->getRepository(Image::class)->findAll()[0]->setTrick());
    return $this->entityManager->getRepository(Image::class)->findAll();
  }

}
