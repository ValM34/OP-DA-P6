<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTimeImmutable;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }
  // CREATE 10 TRICKS
  public function load(ObjectManager $manager): void
  {
    $date = new DateTimeImmutable();
    for ($i = 0; $i < 10; $i++) {
      for ($o = 0; $o < 10; $o++){
        $message = new Message();
        $user = $this->entityManager->getRepository(User::class)->findByEmail('user@email1.com');
        $trick = $this->entityManager->getRepository(Trick::class)->findByUser($user[0]);
        $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam id nulla convallis, vulputate felis non, finibus est. Suspendisse in odio quis elit consequat elementum. Maecenas lectus neque, imperdiet id gravida nec, egestas vehicula orci. Etiam turpis dolor, vulputate ut egestas sit amet, dignissim sed sapien.';
        $message
          ->setTrick($trick[$i])
          ->setUser($user[0])
          ->setUpdatedAt($date)
          ->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
        ;

        $manager->persist($message);
      }
    }

    $manager->flush();
  }

  public function getDependencies()
  {
    return [
      UserFixtures::class,
      TrickFixtures::class,
    ];
  }
}
