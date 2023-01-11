<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTimeImmutable;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
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
      $category = new Category();
      $user = $this->entityManager->getRepository(User::class)->findByEmail('user@email1.com');
      $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam id nulla convallis, vulputate felis non, finibus est. Suspendisse in odio quis elit consequat elementum. Maecenas lectus neque, imperdiet id gravida nec, egestas vehicula orci. Etiam turpis dolor, vulputate ut egestas sit amet, dignissim sed sapien.';
      $category
        ->setName('Category' . $i)
        ->setDescription($description)
        ->setUpdatedAt($date)
        ->setUser($user[0])
      ;

      $manager->persist($category);
    }

    $manager->flush();
  }

  public function getDependencies()
  {
    return [
      UserFixtures::class,
    ];
  }
}
