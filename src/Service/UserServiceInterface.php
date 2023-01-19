<?php 

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormInterface;

interface UserServiceInterface
{
  public function findOne(User $user): User;
  public function create(User $user, FormInterface $form): void;
  public function update(User $user): void;
  public function upload(UploadedFile $file): string;
  public function findByToken(string $token): ?User;
  public function validateUser(User $user): void;
  public function getTargetDirectory(): string;
  public function sendPasswordRecoveryToken(User $user): void;
  public function findByEmail(string $email): ?User;
  public function findByRecoveryToken(string $token): User;
}
