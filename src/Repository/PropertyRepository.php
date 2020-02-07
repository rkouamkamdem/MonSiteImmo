<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @return Property[]
     */
    public function findAllVisible(){
        //On retourne les biens pas encore vendus
        return $this->createQueryBuilder('p')
            ->where('p.sold = false')
            ->getQuery()->getResult();

        //On aurait pu faire ==> return $this->findVisibleQuery()->getQuery()->getResult();
    }

    /**
     * @return Query
     */
    public function findAllVisibleQuery(): Query {
        return $this->findVisibleQuery()->getQuery();
    }

    /**
     * @return Property[]
     */
    //Cette methode permet de récupérer les 5 derniers resultats
    public function findLatest(){
        return $this->findVisibleQuery()
            ->setMaxResults(5)
            ->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    //Cette methode me permet de retourner l'objet QueryBuilder avec tous les biens qui ne sont pas vendus
    private function findVisibleQuery() {
        return $this->createQueryBuilder('p')
            ->where('p.sold = false');
    }

    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
public function searchAvailableProperty($prix,$nbPiece){
    return $this->createQueryBuilder('p')
        ->andWhere('p.price >= :prix')
        ->andWhere('p.bedrooms <= :nbPiece')
        ->andwhere('p.sold = false')
        ->setParameter('prix', $prix)
        ->setParameter('nbPiece', $nbPiece)
        ->orderBy('p.id', 'ASC')
        ->getQuery()
        ->getResult()
        ;
}
    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
