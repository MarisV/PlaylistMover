<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function getUserStats(UserInterface $user): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select(
                'p.provider AS provider',
                'COUNT(DISTINCT p.id) AS playlists_count',
                'COUNT(t.id) AS tracks_count'
            )
            ->from(Playlist::class, 'p')
            ->leftJoin('p.tracks', 't')
            ->where('p.owner = :owner')
            ->groupBy('p.provider')
            ->setParameter('owner', $user);


        $results = $qb->getQuery()->getResult();

        $stats = [];
        foreach ($results as $result) {
            $provider = $result['provider'];
            $stats[$provider] = [
                'playlists_count' => $result['playlists_count'],
                'tracks_count' => $result['tracks_count']
            ];
        }

        return $stats;
    }

}
