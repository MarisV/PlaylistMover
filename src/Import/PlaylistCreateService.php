<?php

namespace App\Import;

use App\Entity\Artist;
use App\Entity\Playlist;
use App\Entity\Track;
use App\Service\Enums\Providers;
use App\Service\Fetcher\Dto\PlaylistDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class PlaylistCreateService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {

    }

    /**
     * @todo: Add try-catch, EM-transactions
     */
    public function createFromApi(array $playlists, Providers $provider, UserInterface $owner): int
    {
        $counter = 0;
        $batchSize = 10;

        foreach ($playlists['data'] as $playlistDto) {
            $playlist = Playlist::fromDTO($playlistDto)
                ->setOwner($owner)
                ->setProvider($provider->value);

            foreach ($playlistDto->getTracks() as $trackDto) {
                $track = Track::fromDTO($trackDto);

                $this->entityManager->persist($track);

                foreach ($trackDto->getArtists() as $artistDto) {
                    $artist = Artist::fromDTO($artistDto);
                    $track->addArtist($artist);
                    $this->entityManager->persist($artist);
                }
                $playlist->addTrack($track);
            }

            $this->entityManager->persist($playlist);

            if ((++$counter % $batchSize) == 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $counter;
    }
}