<?php

namespace App\Repository;

use App\Entity\UserOAuth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserOAuth>
 *
 * @method UserOAuth|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserOAuth|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserOAuth[]    findAll()
 * @method UserOAuth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserOAuthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserOAuth::class);
    }

//    /**
//     * @return UserOAuth[] Returns an array of UserOAuth objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserOAuth
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
