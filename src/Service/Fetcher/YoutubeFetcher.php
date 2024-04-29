<?php

namespace App\Service\Fetcher;

use App\Service\Fetcher\Interface\FetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: "fetcher_provider")]
class YoutubeFetcher extends BaseFetcher implements FetcherInterface
{

    public function fetchPlaylists(): array
    {
        return [
            'count' => 0,
            'data' => []
        ];
    }

    public function fetchTracks()
    {
        // TODO: Implement fetchTracks() method.
    }
}