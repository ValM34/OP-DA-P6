<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
  private $hasher;
  
  public function __construct(UserPasswordHasherInterface $hasher)
  {
    $this->hasher = $hasher;
  }

  // CREATE 20 USERS
  public function load(ObjectManager $manager): void
  {
    $date = new DateTimeImmutable();
    for ($i = 0; $i < 20; $i++) {
      $user = new User();
      $password = $this->hasher->hashPassword($user, 'password');
      $user
        ->setEmail('user@email' . $i . '.com')
        ->setLastName('Dupont' . $i)
        ->setUpdatedAt($date)
        ->setIsVerified(1)
        ->setFirstName('user ' . $i)
        ->setPassword($password)
        ->setAvatar('default.jpg')
      ;

      $manager->persist($user);
    }

    $manager->flush();
  }
}
