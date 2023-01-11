<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IdTrick>
 *
 * @method IdTrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdTrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdTrick[]    findAll()
 * @method IdTrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdTrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdTrick::class);
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
}
