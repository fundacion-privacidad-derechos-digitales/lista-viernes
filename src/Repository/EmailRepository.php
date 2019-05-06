<?php

namespace App\Repository;

use App\Entity\Email;
use App\Entity\User;
use App\Hash\Hasher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Email|null find($id, $lockMode = null, $lockVersion = null)
 * @method Email|null findOneBy(array $criteria, array $orderBy = null)
 * @method Email[]    findAll()
 * @method Email[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailRepository extends ServiceEntityRepository
{
    private $hasher;

    public function __construct(RegistryInterface $registry, Hasher $hasher)
    {
        $this->hasher = $hasher;

        parent::__construct($registry, Email::class);
    }

    public function findOneByValidationToken($token): ?Email
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.validationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEmailHash(string $email)
    {
        return $this->getEntityManager()
            ->getRepository(Email::class)
            ->findBy([
                'emailHash' => $this->hasher->hash(strtolower($email))
            ]);
    }

    public function findByEmailHashValidated(string $email)
    {
        return $this->getEntityManager()
            ->getRepository(Email::class)
            ->findBy([
                'emailHash' => $this->hasher->hash(strtolower($email)),
                'validated' => true
            ]);
    }

    public function deleteAllByUser(User $user)
    {
        return $this->createQueryBuilder('e')
            ->delete(Email::class, 'e')
            ->where('e.user = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function deleteAllByHash(string $email)
    {
        return $this->createQueryBuilder('e')
            ->delete(Email::class, 'e')
            ->where('e.emailHash = :emailHash')
            ->setParameter('emailHash', $this->hasher->hash(strtolower($email)))
            ->getQuery()
            ->getResult();
    }

    public function countAll()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countValidated()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.validated = :validated')
            ->setParameter('validated', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteNonValidatedAfter(string $days)
    {
        $date = new \DateTime();
        $date->sub(date_interval_create_from_date_string("$days days"));

        $qb = $this->createQueryBuilder('e');
        return $qb->select()
            ->andWhere('e.createdAt < :date')
            ->setParameter('date', $date)
            ->andWhere('e.validated = :validated')
            ->setParameter('validated', false)
            ->getQuery()
            ->getResult();
    }
}
