<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\SendMailServiceInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Form;

class UserService implements UserServiceInterface
{
  private $entityManager;
  private $sendMailService;
  private $targetDirectory;
  private $slugger;
  private $userPasswordHasher;

  public function __construct(
    EntityManagerInterface $entityManager,
    string $targetDirectory,
    SluggerInterface $slugger,
    SendMailServiceInterface $sendMailService,
    UserPasswordHasherInterface $userPasswordHasher
  ) {
    $this->entityManager = $entityManager;
    $this->targetDirectory = $targetDirectory;
    $this->slugger = $slugger;
    $this->sendMailService = $sendMailService;
    $this->userPasswordHasher = $userPasswordHasher;
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
  public function create(User $user, FormInterface $form): void
  {
    // encode the plain password
    $passwordEncrypt = $this->userPasswordHasher->hashPassword(
      $user,
      $form->get('plainPassword')->getData()
    );

    $token = hash('sha512', $user->getEmail() . uniqId() . 'dsf51dsf15dsSDFSf521d65s');
    
    $avatar = $form->get('avatar')->getData();
    $avatarPath = $avatar !== null ? $this->upload($avatar) : '../avatarDefault.jpg';

    $user
      ->setAvatar($avatarPath)
      ->setRegistrationToken($token)
      ->setPassword($passwordEncrypt)
    ;

    // Send validation mail
    $this->sendMailService->emailValidation(
      'snow@tricks.fr',
      $user->getEmail(),
      'Activation de votre compte sur le site Snowtricks',
      $token
    );

    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }

  // UPDATE EXCEPT PASSWORD
  public function updateExceptPassword(User $user, User $newsDatasUser): void
  {
    if($newsDatasUser->getFirstName() !== null){
      $user->setFirstName($newsDatasUser->getFirstName());
    }
    if($newsDatasUser->getLastName() !== null){
      $user->setLastName($newsDatasUser->getLastName());
    }
    if($newsDatasUser->getAvatar() !== null){
      $user->setAvatar($newsDatasUser->getAvatar());
    }
    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }

  // UPDATE PASSWORD
  public function updatePassword(User $user, Form $form): void
  {
    $user->setPassword(
      $this->userPasswordHasher->hashPassword(
        $user,
        $form->get('plainPassword')->getData()
      )
    );
    $user->setPasswordRecoveryToken('used');
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
  public function findByToken(string $token): ?User
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
    $user = $this->findByEmail($user->getEmail());
    $token = hash('sha512', $user->getEmail() . uniqId() . 'dsf51dsf15dsSDFSqsdf521d65s');
    $user->setPasswordRecoveryToken($token);
    $this->entityManager->persist($user);
    $this->entityManager->flush();

    $this->sendMailService->passwordRecovery($user, $token);
  }

  public function sendAccountDeletionToken(User $user): void
  {
    $token = hash('sha512', $user->getEmail() . uniqId() . 'dsf51dsf15dsSDFSqsdf521d65s');
    $user->setAccountDeletionToken($token);
    $this->entityManager->persist($user);
    $this->entityManager->flush();
    $this->sendMailService->accountDeletion($user, $token);
  }

  // FIND BY EMAIL
  public function findByEmail(string $email): ?User
  {
    return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
  }

  // FIND BY RECOVERY TOKEN
  public function findByRecoveryToken(string $token): ?User
  {
    return $this->entityManager->getRepository(User::class)->findOneBy(['password_recovery_token' => $token]);
  }

  // FIND BY ID
  public function findByAccountDeletionToken(string $token): ?User
  {
    return $this->entityManager->getRepository(User::class)->findOneBy(['account_deletion_token' => $token]);
  }

  // DELETE
  public function delete(User $user): void
  {
    $this->entityManager->remove($user);
    $this->entityManager->flush();
  }
}
