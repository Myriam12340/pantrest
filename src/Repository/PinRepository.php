<?php

namespace App\Repository;

use App\Entity\Pin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pin[]    findAll()
 * @method Pin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pin::class);
    }





    // /**
    //  * @return Pin[] Returns an array of Pin objects
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

    /*
    public function findOneBySomeField($value): ?Pin
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllLIKEDESCRIPTION(string $description): array
    { $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Pin p
            WHERE (p.description like :description OR p.title like :description) and ( p.audience like :Public)
            ORDER BY p.createdAt DESC'
        )->setParameter('description', '%'.$description.'%')
        ->setParameter('Public', 'Public');
        // returns an array of Product objects
        return $query->getResult();
    }


    public function findAllby2(string $description ,string $ca ): array
    { $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Pin p
            WHERE (p.description like :description OR p.title like :description) and ( p.audience like :Public) and ( p.categorie like :categorie)
            ORDER BY p.createdAt DESC'
        )->setParameter('description', '%'.$description.'%')
            ->setParameter('Public', 'Public')

            ->setParameter('categorie', $ca)
        ;
        // returns an array of Product objects
        return $query->getResult();
    }



}
