<?php

namespace App\Repository;

use App\Entity\CompareTemp;
use App\Entity\Email;
use App\Entity\MatchJob;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Email|null find($id, $lockMode = null, $lockVersion = null)
 * @method Email|null findOneBy(array $criteria, array $orderBy = null)
 * @method Email[]    findAll()
 * @method Email[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchJobRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MatchJob::class);
    }

    public function findFirstPending(){
        return $this->createQueryBuilder('m')
            ->where('m.status = :status')
            ->setParameter('status', MatchJob::STATUS_PENDING)
            ->orderBy('m.createdAt', 'asc')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }


}
