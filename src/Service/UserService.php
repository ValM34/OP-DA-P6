<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\SendMailServiceInterface;

class UserService implements UserServiceInterface
{
  private $entityManager;
  private $sendMailService;
  private $targetDirectory;
  private $slugger;

  public function __construct(EntityManagerInterface $entityManager, string $targetDirectory, SluggerInterface $slugger, SendMailServiceInterface $sendMailService)
  {
    $this->entityManager = $entityManager;
    $this->targetDirectory = $targetDirectory;
    $this->slugger = $slugger;
    $this->sendMailService = $sendMailService;
  }

  // FIND ONE
  public function findOne(User $user): User
  {
    $user = $this->entityManager->getRepository(User::class)->find($user);
    if (!$user) {
      $user = null;
    }

    return $user;
  }

  // CREATE
  public function create(User $user, string $avatarPath = 'default.jpg'): void
  {
    $user->setAvatar($avatarPath);
    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }

  // UPDATE
  public function update(User $user): void
  {
    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }

  // UPLOAD AVATAR
  public function upload(UploadedFile $file): string
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

  public function getTargetDirectory(): string
  {
    return $this->targetDirectory;
  }

  // FIND BY TOKEN
  public function findByToken(string $token): User
  {
    return $this->entityManager->getRepository(User::class)->findOneBy(['registration_token' => $token]);
  }

  // VALIDATE USER
  public function validateUser(User $user): void
  {
    $user->setIsVerified(true);
    $user->setRegistrationToken('valid');
    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }

  // SEND PASSWORD VALIDATION TOKEN
  public function sendPasswordRecoveryToken(User $user): void
  {
    // Je vais envoyer le token en BDD et envoyer un mail à l'utilisateur en question.
    $token = hash('sha512', $user->getEmail() . uniqId() . 'dsf51dsf15dsSDFSqsdf521d65s');
    $user->setPasswordRecoveryToken($token);
    $this->entityManager->persist($user);
    $this->entityManager->flush();

    $this->sendMailService->passwordRecovery($user, $token);
  }

  // FIND BY EMAIL
  public function findByEmail(string $email): User
  {
    return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
  }

  // FIND BY RECOVERY TOKEN
  public function findByRecoveryToken(string $token): User
  {
    return $this->entityManager->getRepository(User::class)->findOneBy(['password_recovery_token' => $token]);
  }
}
