<?php

namespace App\Repository;

use App\Entity\Helper;
use App\MatchFinder\ZipCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Helper|null find($id, $lockMode = null, $lockVersion = null)
 * @method Helper|null findOneBy(array $criteria, array $orderBy = null)
 * @method Helper[]    findAll()
 * @method Helper[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelperRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Helper::class);
    }

    public function clearOldProposal(string $email)
    {
        foreach ($this->findBy(['email' => strtolower($email)]) as $proposal) {
            $this->_em->remove($proposal);
        }

        $this->_em->flush();
    }

    public function findClosestHelpersTo(string $zipCode)
    {
        $query = $this->createQueryBuilder('h')
            ->select('h', 'r')
            ->leftJoin('h.requests', 'r')
            ->where('h.zipCode = :zipCode')
            ->setParameter('zipCode', $zipCode)
            ->orderBy('h.createdAt', 'DESC')
        ;

        if ($closestZipCode = ZipCode::CLOSEST[$zipCode] ?? null) {
            $query->orWhere('h.zipCode = :closestZipCode')
                ->setParameter('closestZipCode', $closestZipCode);
        }

        $helpers = $query->getQuery()->getResult();

        return array_filter($helpers, fn (Helper $helper) => 0 === $helper->getRequests()->count());
    }

    public function export()
    {
        return $this->createQueryBuilder('h')
            ->select('h.firstName', 'h.lastName', 'h.email')
            ->orderBy('h.createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
