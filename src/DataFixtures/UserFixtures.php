<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
  
  public function __construct(private UserPasswordHasherInterface $hasher)
  {}

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
        ->setAvatar('../avatarDefault.jpg')
      ;

      $manager->persist($user);
    }

    $manager->flush();
  }
}
