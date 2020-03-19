<?php

namespace App\Repository;

use App\Entity\BlockedMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BlockedMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockedMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockedMatch[]    findAll()
 * @method BlockedMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockedMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockedMatch::class);
    }

    public function findBlockedHelpersIdsFor(string $ownerUuid)
    {
        $blockedMatches = $this->createQueryBuilder('b')
            ->select('DISTINCT h.id')
            ->leftJoin('b.helper', 'h')
            ->where('b.ownerUuid = :ownerUuid')
            ->setParameter('ownerUuid', $ownerUuid)
            ->getQuery()
            ->getScalarResult()
        ;

        return array_column($blockedMatches, 'id');
    }
}
