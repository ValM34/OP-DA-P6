<?php 

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Video;

interface VideoServiceInterface
{
  public function create(Trick $trick, array $arrayOfVideos): void;
  public function delete(Video $video): ?string;
}
