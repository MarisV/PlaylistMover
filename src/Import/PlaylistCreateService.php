<?php

namespace App\Import;

use App\Entity\Artist;
use App\Entity\Playlist;
use App\Entity\Track;
use App\Entity\User;
use App\Repository\PlaylistRepository;
use App\Repository\TrackRepository;
use App\Service\Enums\Providers;
use App\Service\Fetcher\Dto\PlaylistDto;
use App\Service\Fetcher\Dto\TrackDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class PlaylistCreateService
{
    private array $tracksidentifiers;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TrackRepository $trackRepository,
        private PlaylistRepository $playlistRepository
    ) {
        //@todo: Maybe store isrc in some cache/redis?
        $this->tracksidentifiers = $this->trackRepository->getIdentifiers();
    }

    /**
     * @todo: Add try-catch, EM-transactions
     */
    public function createFromApi(array $playlists, Providers $provider, UserInterface $owner): int
    {
        $counter = 0;
        $batchSize = 10;

        foreach ($playlists['data'] as $playlistDto) {

            $playlist = $this->getPlaylistOrCreate($playlistDto);
            $playlist
                ->setOwner($this->entityManager->getReference(User::class, $owner->getId()))
                ->setProvider($provider->value);

            foreach ($playlistDto->getTracks() as $trackDto) {
                $track = $this->findTrackOrCreate($trackDto);

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


    private function getPlaylistOrCreate(PlaylistDto $playlistDto)
    {
        if ($playlistDto->getProviderId()) {
            $playlist = $this->playlistRepository->findOneBy([
                'providerId' => $playlistDto->getProviderId(),
            ]);

            if ($playlist) {
                return $playlist;
            }
        }

        $playlist = Playlist::fromDTO($playlistDto);
        $this->entityManager->persist($playlist);

        return $playlist;
    }

    private function findTrackOrCreate(TrackDto $trackDto): Track
    {
        if ($trackDto->getIsrc()) {
            $track = $this->trackRepository->findOneBy([
                'isrc' => $trackDto->getIsrc()
            ]);

            if ($track) {
                return $track;
            }
        }
        $track = Track::fromDTO($trackDto);
        $this->entityManager->persist($track);

        return $track;
    }

}