<?php

namespace App\Repository;

use App\Entity\TransactionComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TransactionComment>
 *
 * @method TransactionComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionComment[]    findAll()
 * @method TransactionComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionComment::class);
    }

//    /**
//     * @return TransactionComment[] Returns an array of TransactionComment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TransactionComment
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
