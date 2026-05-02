<?php

namespace App\Repository;

use App\Entity\Atelier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Atelier>
 */
class AtelierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Atelier::class);
    }

    /**
     * @return list<Atelier>
     */
    public function findPublishedOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.archived = :archived')
            ->andWhere('a.status = :status')
            ->setParameter('archived', false)
            ->setParameter('status', 'published')
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
