<?php 

namespace App\Service;

use App\Entity\Image;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageServiceInterface
{
  public function findAll();
  public function create(Trick $trick, $arrayOfImages): void;
  public function upload(UploadedFile $file);
  public function delete(int $id);
  public function deleteByTrick(array $images): void;
}
