<?php

namespace App\Repository;

use App\Entity\Humeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Humeur>
 *
 * @method Humeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Humeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Humeur[]    findAll()
 * @method Humeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HumeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Humeur::class);
    }

    public function add(Humeur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Humeur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @return Humeur[] Returns an array of Humeur objects
     */
    public function findByUserAndYear($user, $beginningOfYear, $endOfYear): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.user = :user')
            ->andWhere('h.created_at BETWEEN :minDate AND :maxDate')
            ->setParameters(['user' => $user,'minDate' => $beginningOfYear, 'maxDate' => $endOfYear])
            ->orderBy('h.created_at', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Humeur[] Returns an array of Humeur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Humeur
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
