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

  public function findAllTricks()
  {
    return $this->createQueryBuilder('t')
      ->select('t.name', 't.slug', 'i.path')
      ->leftJoin('t.images', 'i')
      ->orderBy('t.updated_at', 'DESC')
      ->getQuery()
      ->getResult()
    ;
  }

  public function findTrick(string $slug)
  {
    return $this->createQueryBuilder('t')
      ->select('t', 'i', 'm', 'v', 'u')
      ->leftJoin('t.images', 'i')
      ->leftJoin('t.videos', 'v')
      ->leftJoin('t.messages', 'm')
      ->orderBy('m.created_at', 'DESC')
      ->leftJoin('m.user', 'u')
      ->andWhere('t.slug = :slug')
      ->setParameter('slug', strtolower($slug)) // @TODO modifier aussi pour les autres slug
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }

  public function getMessages(string $slug, int $page, int $limit)
  {
    return $this->createQueryBuilder('t')
      ->select('t', 'm', 'u')
      ->leftJoin('t.messages', 'm')
      ->leftJoin('m.user', 'u')
      ->orderBy('m.created_at', 'DESC')
      ->andWhere('t.slug = :slug')
      ->setParameter('slug', $slug)
      ->setFirstResult(($page - 1) * $limit)
      ->setMaxResults($limit)
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }
}
