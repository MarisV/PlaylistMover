<?php

namespace App\Model;

use App\Entity\Playlist;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;

readonly class PlaylistsStatsModel
{
    private Collection $providers;
    private Collection $playlists;


    public function __construct(private UserInterface $user)
    {
        $this->providers = $this->user->getUserOAuths();
        $this->playlists = $this->user->getPlaylists();
    }

    public function getPlaylistsStats(): array
    {
        $result = [];

        foreach ($this->providers as $provider) {

            $playlists = $this->playlists->filter(function (Playlist $playlist) use ($provider) {
                return $playlist->getProvider() === $provider->getProvider();
            });

            $totalTracks = $playlists->reduce(function ($carry, $playlist) {
                return $carry + $playlist->getTracks()->count();
            }, 0);

            $result[$provider->getProvider()] = [
                'playlists_count' => $playlists->count(),
                'tracks_count' => $totalTracks
            ];
        }

        return $result;
    }
}