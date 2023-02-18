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
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager, protected SluggerInterface $slugger)
  {
    $this->entityManager = $entityManager;
  }
  // CREATE 10 TRICKS
  public function load(ObjectManager $manager): void
  {
    $date = new DateTimeImmutable();
    for ($i = 0; $i < 20; $i++) {
      $trick = new Trick();
      $user = $this->entityManager->getRepository(User::class)->findByEmail('user@email1.com');
      $category = $this->entityManager->getRepository(Category::class)->findByUser($user);
      $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam id nulla convallis, vulputate felis non, finibus est. Suspendisse in odio quis elit consequat elementum. Maecenas lectus neque, imperdiet id gravida nec, egestas vehicula orci. Etiam turpis dolor, vulputate ut egestas sit amet, dignissim sed sapien. Etiam elementum, risus vel vehicula fringilla, arcu felis blandit metus, at suscipit magna quam sed libero. Nunc consectetur ex eget tortor consectetur molestie. Vestibulum malesuada nec nulla tincidunt porta. Nulla vulputate est nec nisl eleifend, nec tempus dolor dapibus. Aliquam a egestas libero. Suspendisse felis odio, accumsan id lorem a, semper cursus tellus. Sed gravida purus nisl, vitae consectetur augue tincidunt non. Integer lobortis lorem justo, a convallis dui imperdiet nec. Phasellus ut elementum sem. Suspendisse scelerisque est ut nisi blandit, eget mollis magna ultrices. Cras auctor risus nec risus accumsan, in tristique magna imperdiet. Morbi non hendrerit nulla, quis pharetra ipsum. Donec a ante ut nunc interdum facilisis. Integer tempus faucibus enim, id venenatis mauris aliquam quis. Nulla facilisi. Donec tellus libero, posuere sed quam at, sagittis blandit dui. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Cras quis venenatis eros.';
      $trick
        ->setName('Trick' . $i)
        ->setDescription($description)
        ->setUpdatedAt($date)
        ->setUser($user[0])
        ->setCategory($category[0])
        ->setSlug(strtolower($this->slugger->slug($trick->getName())))
      ;

      $manager->persist($trick);
    }

    $manager->flush();
  }

  public function getDependencies()
  {
    return [
      UserFixtures::class,
      CategoryFixtures::class,
    ];
  }
}
