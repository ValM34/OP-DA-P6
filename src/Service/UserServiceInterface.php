<?php 

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Form;

interface UserServiceInterface
{
  public function findOne(User $user): User;
  public function create(User $user, FormInterface $form): void;
  public function updateExceptPassword(User $user, User $newsDatasUser, FormInterface $form): void;
  public function updatePassword(User $user, Form $form): void;
  public function upload(UploadedFile $file): string;
  public function findByToken(string $token): ?User;
  public function validateUser(User $user): void;
  public function getTargetDirectory(): string;
  public function sendPasswordRecoveryToken(User $user): void;
  public function findByEmail(string $email): ?User;
  public function findByRecoveryToken(string $token): ?User;
  public function findByAccountDeletionToken(string $token): ?User;
  public function delete(User $user): void;
  public function sendAccountDeletionToken(User $user): void;
}
