<?php

namespace App\Repository;

use App\Entity\UserOAuth;
use App\Service\Enums\Providers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

   /**
    * @return UserOAuth[] Returns an array of UserOAuth objects
    */
   public function findByUser(UserInterface $user): array
   {
       return $this->createQueryBuilder('u')
           ->andWhere('u.user = :user')
           ->setParameter('user', $user)
           ->orderBy('u.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

    /**
     * @param Providers $provider
     * @return UserOAuth[]
     */
    public function findForTokenRefresh(Providers $provider): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.createdAt <= :created')
            ->andWhere('u.provider = :provider')
            ->setParameters([
                'created' => new \DateTime('-1 minutes'),
                'provider' => $provider->value,
            ])
            ->getQuery()
            ->getResult();
    }
}
