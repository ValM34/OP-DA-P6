<?php

namespace App\Service;

use \DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Category;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request; 

class UserService implements UserServiceInterface
{
  private $entityManager;
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
    $this->request = new Request(
      $_GET,
      $_POST,
      [],
      $_COOKIE,
      $_FILES,
      $_SERVER
    );
  }

  public function findOne(User $user)
  {
    $user = $this->entityManager->getRepository(User::class)->find($user);
    if (!$user) {
      $user = null;
    }

    return $user;
  }
}
