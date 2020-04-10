<?php

namespace App\Repository;

use App\Entity\Helper;
use App\Entity\HelpRequest;
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
    protected string $locale;

    public function __construct(ManagerRegistry $registry, string $locale)
    {
        parent::__construct($registry, Helper::class);

        $this->locale = $locale;
    }

    public function removeHelpProposal(string $email)
    {
        foreach ($this->findBy(['email' => strtolower($email)]) as $proposal) {
            $this->_em->createQueryBuilder()
                ->update()
                ->from(HelpRequest::class, 'r')
                ->set('r.matchedWith', 'NULL')
                ->set('r.finished', 'false')
                ->where('r.matchedWith = :matchedWith')
                ->setParameter('matchedWith', $proposal->getId())
                ->getQuery()
                ->execute()
            ;

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

        if ($closestZipCodes = ZipCode::CLOSEST[$this->locale][$zipCode] ?? null) {
            $query->orWhere('h.zipCode = :closestZipCode')->setParameter('closestZipCode', $closestZipCodes[0]);
            $query->orWhere('h.zipCode = :closestZipCode')->setParameter('closestZipCode', $closestZipCodes[1]);
        }

        $helpers = $query->getQuery()->getResult();

        return array_filter($helpers, fn (Helper $helper) => 0 === $helper->getRequests()->count());
    }

    public function exportAll()
    {
        return $this->createQueryBuilder('h')
            ->select('h.firstName', 'h.lastName', 'h.email')
            ->orderBy('h.createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function exportByZipCode()
    {
        return $this->createQueryBuilder('h')
            ->select('h.zipCode', 'COUNT(h) AS nb')
            ->orderBy('nb', 'DESC')
            ->groupBy('h.zipCode')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * @return array
     */
    public function findAssoEntourage()
    {
        $qb = $this->createQueryBuilder('h');

        $matchedIds = $this->_em->createQueryBuilder()
            ->select('h.id')
            ->from(HelpRequest::class, 'r')
            ->leftJoin('r.matchedWith', 'h')
            ->where('r.matchedWith IS NOT NULL')
            ->getQuery()
            ->getArrayResult()
        ;

        return $qb
            ->select('h.firstName', 'h.email')
            ->where('h.age >= 50')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->in('h.zipCode', ['35000', '35200', '35700', '59000', '59160', '59260', '59777', '59800']),
                'h.zipCode LIKE \'75%\'',
                'h.zipCode LIKE \'92%\'',
                'h.zipCode LIKE \'6900%\''
            ))
            ->andWhere($qb->expr()->notIn('h.id', array_column($matchedIds, 'id')))
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
