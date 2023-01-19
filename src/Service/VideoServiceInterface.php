<?php 

namespace App\Service;

use App\Entity\Trick;

interface VideoServiceInterface
{
  public function create(Trick $trick, array $arrayOfVideos): void;
  public function delete(int $id): ?int;
}
