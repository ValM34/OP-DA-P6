<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserService implements UserServiceInterface
{
  private $entityManager;
  public function __construct(EntityManagerInterface $entityManager, $targetDirectory, SluggerInterface $slugger)
  {
    $this->entityManager = $entityManager;
    $this->targetDirectory = $targetDirectory;
    $this->slugger = $slugger;
    $this->request = new Request(
      $_GET,
      $_POST,
      [],
      $_COOKIE,
      $_FILES,
      $_SERVER
    );
  }

  // FIND ONE
  public function findOne(User $user)
  {
    $user = $this->entityManager->getRepository(User::class)->find($user);
    if (!$user) {
      $user = null;
    }

    return $user;
  }

  // CREATE
  public function create(User $user, string $avatarPath = 'default.jpg')
  {
    $user->setAvatar($avatarPath);
    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }

  // UPLOAD
  public function upload(UploadedFile $file)
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

  public function getTargetDirectory()
  {
    return $this->targetDirectory;
  }

  // FIND BY TOKEN
  public function findByToken(string $token)
  {
    return $this->entityManager->getRepository(User::class)->findOneBy(['registration_token' => $token]);
  }

  // VALIDATE USER
  public function validateUser(User $user)
  {
    $user->setIsVerified(true);
    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }
}
