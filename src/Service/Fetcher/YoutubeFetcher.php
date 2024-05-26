<?php

namespace App\Service\Fetcher;

use App\Service\Enums\Providers;
use App\Service\Fetcher\Dto\ArtistDto;
use App\Service\Fetcher\Dto\PlaylistDto;
use App\Service\Fetcher\Dto\TrackDto;
use App\Service\Fetcher\Interface\FetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: "fetcher_provider")]
class YoutubeFetcher extends BaseFetcher implements FetcherInterface
{
    public const NAME = Providers::YOUTUBE;

    private CONST LIMIT = 50;

    private const PLAYLISTS_URL = 'https://youtube.googleapis.com/youtube/v3/playlists?';
    private const TRACKS_URL = 'https://youtube.googleapis.com/youtube/v3/playlistItems?';

    public function fetchPlaylistsData(): array
    {

        $items = [];
        $url = $this->buildUrl();
        do{
            $response = $this->makeRequest($url);
            $responseData = $response->toArray();

            $currentItems = $responseData['items'] ?? [];
            $items = array_merge($items, $currentItems);

            $pageToken = $responseData['nextPageToken'] ?? null;
            $url = $pageToken ? $url . '&pageToken=' . $pageToken : null;

        } while ($url);

        $data = [];
        foreach($items as $item) {
            $snippet = $item['snippet'];

            $playList = new PlaylistDto(
                $this->user,
                $snippet['title'],
                self::NAME->value,
                $snippet['thumbnails']['default']['url'],
                null,
                $item['id'],
                $this->fetchTracks($item['id'])
            );

            $data[] = $playList;
        }


        return [
            'count' => count($data),
            'data' => $data
        ];
    }


    public function fetchTracks(string $playlistId): array
    {
        $items = [];
        $params = [
            'part' => 'snippet',
            'playlistId' => $playlistId,
            'maxResults' => self::LIMIT,
            'key' => $this->secrets->get('youtube_abi_key')
        ];

        $url = self::TRACKS_URL . http_build_query($params);

        do {
            $response = $this->makeRequest($url)->getContent();

            $responseData = json_decode($response, true);

            $currentItems = $responseData['items'] ?? [];
            $items = array_merge($items, $currentItems);

            $pageToken = $responseData['nextPageToken'] ?? null;
            $url = $pageToken ? $url . '&pageToken=' . $pageToken : null;

        } while ($url);

        $tracks = [];
        foreach ($items as $row) {
            $item = $row['snippet'];

            $track = new TrackDto(
                $row['id'],
                $item['title'],
                'https://www.youtube.com/watch?v=' . $item['resourceId']['videoId'],
                $item['position'],
                $row['etag']
            );

            $track->addArtist(
                new ArtistDto(
                    $item['videoOwnerChannelId'] ?? '-',
                    str_replace(' - Topic', '', $item['videoOwnerChannelTitle'] ?? ''),
                    null
                )
            );
            $tracks[] = $track;
        }
        return $tracks;
    }


    private function buildUrl(): string
    {
        return self::PLAYLISTS_URL .
            http_build_query([
                'part' => 'contentDetails,snippet',
                'maxResults' => self::LIMIT,
                'mine' => true,
                'key' => $this->secrets->get('youtube_abi_key')

            ]);
    }
}

