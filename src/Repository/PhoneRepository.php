<?php

namespace App\Repository;

use App\Entity\Phone;
use App\Entity\User;
use App\Hash\Hasher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Phone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Phone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Phone[]    findAll()
 * @method Phone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneRepository extends ServiceEntityRepository
{
    private $hasher;

    public function __construct(RegistryInterface $registry, Hasher $hasher)
    {
        $this->hasher = $hasher;

        parent::__construct($registry, Phone::class);
    }

    public function findByPhoneHash(string $phone)
    {
        return $this->getEntityManager()
            ->getRepository(Phone::class)
            ->findBy([
                'phoneHash' => $this->hasher->hash(preg_replace('/[^0-9]/', '', $phone))
            ]);
    }

    public function deleteAllByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->delete(Phone::class, 'p')
            ->where('p.user = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function countAll()
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
