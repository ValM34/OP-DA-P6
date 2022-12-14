<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trick>
 *
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Trick::class);
  }

  public function add(Trick $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Trick $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function findAll()
  {
    return $this->createQueryBuilder('t')
      ->select('t', 'i')
      ->leftJoin('t.images', 'i')
      ->orderBy('t.updated_at', 'DESC')
      ->getQuery()
      ->getResult()
    ;
  }

  //    /**
  //     * @return Trick[] Returns an array of Trick objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('t')
  //            ->andWhere('t.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('t.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Trick
  //    {
  //        return $this->createQueryBuilder('t')
  //            ->andWhere('t.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }


  // @TODO => voir si cette fonction sert toujours ?? quelque chose
  public function findByTrick($trick): array
  {
    $entityManager = $this->getEntityManager();

    /*
  $query = $entityManager->createQuery(
    'SELECT m, u
    FROM App\Entity\Message m
    JOIN App\Entity\User u
    WITH m.id_user = u.id
    WHERE m.id_trick = :id_trick'
  )->setParameter('id_trick', $id_trick);
*/
    /* Equivalent sur mysql ??  : 
    SELECT p.*, u.*
    FROM message p
    JOIN user u
    ON p.id_user_id = u.id
    WHERE p.id_trick_id = 14;
    */



    return $this->createQueryBuilder('p')
      ->select('p', 'u')
      ->leftJoin('p.image', 'u')
      ->where('p.trick = :trick')
      ->setParameter('trick', $trick)
      ->getQuery()
      ->getResult();


    /*
  $queryBuilder = $this->createQueryBuilder('m')
    ->select('m', 'u')
    ->leftJoin('m.id_user', 'u')
    ->where('m.id_trick = :id_trick')
    ->setParameter('id_trick', $id_trick);

  $query = $queryBuilder->getQuery();
  $results = $query->getArrayResult();

  return $results;
  */
  }
}
