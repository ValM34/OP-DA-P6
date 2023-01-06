<?php 

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UserServiceInterface
{
  public function findOne(User $user);
  public function create(User $user, string $avatarPath = 'default.jpg');
  public function update(User $user);
  public function upload(UploadedFile $file);
  public function findByToken(string $token);
  public function validateUser(User $user);
  public function sendPasswordRecoveryToken(User $user);
  public function findByEmail(string $email);
  public function findByRecoveryToken(string $token);
}
