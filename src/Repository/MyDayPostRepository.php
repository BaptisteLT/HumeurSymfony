<?php

namespace App\Repository;

use App\Entity\MyDayPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MyDayPost>
 *
 * @method MyDayPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MyDayPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MyDayPost[]    findAll()
 * @method MyDayPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MyDayPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MyDayPost::class);
    }

    public function add(MyDayPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MyDayPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MyDayPost[] Returns an array of MyDayPost objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MyDayPost
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findBySlugAndNotId($slug ,$post): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.slug = :slug')
            ->andWhere('m.id <> :post')
            ->setParameters(['slug' => $slug,'post' => $post])
            ->getQuery()
            ->getResult()
        ;
    }

}
