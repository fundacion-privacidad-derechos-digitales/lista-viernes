<?php

namespace App\Repository;

use App\Entity\User;
use App\Hash\Hasher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private $hasher;

    public function __construct(RegistryInterface $registry, Hasher $hasher)
    {
        $this->hasher = $hasher;

        parent::__construct($registry, User::class);
    }

    public function findOneByEmailValidationToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.emailValidationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByEmailAndIdNumber(string $email, string $idNumber): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.emailHash = :emailHash')
            ->setParameter('emailHash', $this->hasher->hash(strtolower($email)))
            ->andWhere('u.idNumberHash = :idNumberHash')
            ->setParameter('idNumberHash', $this->hasher->hash(preg_replace('/[^A-Z0-9]/', '', strtoupper($idNumber))))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByEmailHash(string $email)
    {
        return $this->getEntityManager()
            ->getRepository(User::class)
            ->findOneBy(['emailHash' => $this->hasher->hash(strtolower($email))]);
    }

    public function findByEmailHashValidated(string $email)
    {
        return $this->getEntityManager()
            ->getRepository(User::class)
            ->findBy([
                'emailHash' => $this->hasher->hash(strtolower($email)),
                'emailValidated' => true
            ]);
    }

    public function findByIdNumberHashValidated(string $idNumber)
    {
        return $this->getEntityManager()
            ->getRepository(User::class)
            ->findBy([
                'idNumberHash' => $this->hasher->hash(preg_replace('/[^A-Z0-9]/', '', strtoupper($idNumber))),
                'emailValidated' => true
            ]);
    }

    public function countAll()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countValidated()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.emailValidated = :validated')
            ->setParameter('validated', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteNonValidatedAfter(string $days)
    {
        $date = new \DateTime();
        $date->sub(date_interval_create_from_date_string("$days days"));

        $qb = $this->createQueryBuilder('u');
        return $qb->delete()
            ->andWhere('u.createdAt < :date')
            ->setParameter('date', $date)
            ->andWhere('u.emailValidated = :validated')
            ->setParameter('validated', false)
            ->getQuery()
            ->getResult();
    }
}
