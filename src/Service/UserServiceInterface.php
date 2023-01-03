<?php 

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UserServiceInterface
{
  public function findOne(User $user);
  public function create(User $user, string $avatarPath = 'default.jpg');
  public function upload(UploadedFile $file);
}
