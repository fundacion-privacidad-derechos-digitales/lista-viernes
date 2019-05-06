<?php

namespace App\Repository;

use App\Entity\CompareTemp;
use App\Entity\Email;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Email|null find($id, $lockMode = null, $lockVersion = null)
 * @method Email|null findOneBy(array $criteria, array $orderBy = null)
 * @method Email[]    findAll()
 * @method Email[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompareTempRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompareTemp::class);
    }

    public function loadCSV($path)
    {
        $sql = " 
            LOAD DATA LOCAL INFILE ?
            INTO TABLE compare_temp
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\r\n'
         ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $path);
        $stmt->execute();
    }

    public function deleteTable()
    {
        $sql = "DELETE FROM compare_temp WHERE temp <> ''";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
    }

    public function compareEmails()
    {
        $sql = " 
            SELECT c.temp
            FROM compare_temp c INNER JOIN email e ON c.temp = e.email_hash
            WHERE e.validated = 1
         ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function comparePhones()
    {
        $sql = " 
            SELECT c.temp
            FROM compare_temp c INNER JOIN phone p ON c.temp = p.phone_hash
         ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function deleteAllByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->delete(CompareTemp::class, 'c')
            ->where('c.temp <> :temp')
            ->setParameter('temp', '')
            ->getQuery()
            ->getResult();
    }

}
