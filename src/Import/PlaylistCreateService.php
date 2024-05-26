<?php

namespace App\Import;

use App\Entity\Artist;
use App\Entity\Playlist;
use App\Entity\Track;
use App\Entity\User;
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
    ) {

    }

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
        return $this->findEntityOrCreate(
            Playlist::class,
            $playlistDto,
            'providerId'
        );
    }

    private function findTrackOrCreate(TrackDto $trackDto): Track
    {
        return $this->findEntityOrCreate(
            Track::class,
            $trackDto,
            'isrc'
        );
    }

    private function findArtistOrCreate(ArtistDto $artistDto): Artist
    {
        return $this->findEntityOrCreate(
            Artist::class,
            $artistDto,
            'name'
        );
    }

    private function findEntityOrCreate(string $entityClass, $dto, string $identifierField): object
    {
        $entity = $entityClass::fromDTO($dto);

        $scheduledForInsert = $this->entityManager->getUnitOfWork()->getScheduledEntityInsertions();
        $persisted = array_filter($scheduledForInsert, function(object $scheduledEntity) use ($entity, $identifierField) {
            return get_class($scheduledEntity) === get_class($entity) && $entity->{'get' . ucfirst($identifierField)}() === $scheduledEntity->{'get' . ucfirst($identifierField)}();
        });

        if (count($persisted) > 0) {
            return reset($persisted);
        }

        $repository = $this->entityManager->getRepository($entityClass);
        $existingEntity = $repository->findOneBy([
            $identifierField => $entity->{'get' . ucfirst($identifierField)}()
        ]);

        if (!$existingEntity) {
            $this->entityManager->persist($entity);
            return $entity;
        } else {
            return $existingEntity;
        }
    }

}