<?php

namespace App\Import;

use App\Entity\Playlist;
use App\Service\Enums\Providers;
use App\Service\Fetcher\Dto\PlaylistDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class PlaylistCreateService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    public function createFromApi(array $playlists, Providers $provider, UserInterface $owner): int
    {

        $counter = 0;
        $batchSize = 10;

        /** @var PlaylistDto $playlist */
        foreach ($playlists as $playlist) {
            $playlist = (new Playlist())
                ->setOwner($owner)
                ->setTitle($playlist->getTitle())
                ->setProvider($provider->value)
                ->setImageUri($playlist->getImageUri())
                ->setProviderUri($playlist->getProviderUri())
                ->setProviderId($playlist->getProviderId());



            $this->entityManager->persist($playlist);

            if ((++$counter % $batchSize) == 0) {
                $this->entityManager->flush();
            }
        }
        $this->entityManager->flush();

        return $counter;
    }
}