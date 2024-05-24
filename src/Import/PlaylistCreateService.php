<?php

namespace App\Import;

use App\Entity\Artist;
use App\Entity\Playlist;
use App\Entity\Track;
use App\Entity\User;
use App\Repository\ArtistRepository;
use App\Repository\PlaylistRepository;
use App\Repository\TrackRepository;
use App\Service\Enums\Providers;
use App\Service\Fetcher\Dto\ArtistDto;
use App\Service\Fetcher\Dto\PlaylistDto;
use App\Service\Fetcher\Dto\TrackDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class PlaylistCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TrackRepository $trackRepository,
        private PlaylistRepository $playlistRepository,
        private ArtistRepository $artistRepository,
    ) {

    }

    /**
     * @todo: Add try-catch, EM-transactions
     */
    public function createFromApi(array $playlists, Providers $provider, UserInterface $owner): int
    {
        $counter = 0;
        $batchSize = 5;

        foreach ($playlists['data'] as $playlistDto) {

            $playlist = $this->findPlaylistOrCreate($playlistDto);
            $playlist
                ->setOwner($this->entityManager->getReference(User::class, $owner->getId()))
                ->setProvider($provider->value);

            foreach ($playlistDto->getTracks() as $trackDto) {
                $track = $this->findTrackOrCreate($trackDto);
                foreach ($trackDto->getArtists() as $artistDto) {
                    $artist = $this->findArtistOrCreate($artistDto);
                    $track->addArtist($artist);
                }
                $playlist->addTrack($track);
            }

            $this->entityManager->persist($playlist);

            $this->entityManager->flush();
            $this->entityManager->clear();

            if ((++$counter % $batchSize) == 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $counter;
    }

    private function findPlaylistOrCreate(PlaylistDto $playlistDto)
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

    private function findArtistOrCreate(ArtistDto $artistDto): Artist
    {
        $artist = Artist::fromDTO($artistDto);

        //@todo: Use same approach or Playlists and Tracks
        $scheduledForInsert = $this->entityManager->getUnitOfWork()->getScheduledEntityInsertions();;
        $persisted = array_filter($scheduledForInsert, function(object $entity) use ($artist)  { // So do not persist multiple times
            return $entity instanceof Artist && $artist->getName() == $entity->getName();
        });

        if (count($persisted) > 0) {
            return reset($persisted);
        }

        $existingArtistEntity = $this->artistRepository->findOneBy([
            'name' => $artistDto->getName()
        ]);

        if (!$existingArtistEntity) {
            $this->entityManager->persist($artist);
            return $artist;
        } else {
            return $existingArtistEntity;
        }
    }

}