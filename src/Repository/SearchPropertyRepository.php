<?php

namespace App\Repository;

use App\Entity\SearchProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SearchProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchProperty[]    findAll()
 * @method SearchProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchPropertyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SearchProperty::class);
    }

    // /**
    //  * @return SearchProperty[] Returns an array of SearchProperty objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SearchProperty
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
