<?php 

namespace App\Service;

use App\Entity\Trick;

interface VideoServiceInterface
{
  public function create(Trick $trick, $arrayOfVideos);
  public function delete(int $id);
}
