<?php

namespace App\Service\Fetcher;

use App\Service\Enums\Providers;
use App\Service\Fetcher\Dto\ArtistDto;
use App\Service\Fetcher\Dto\PlaylistDto;
use App\Service\Fetcher\Dto\TrackDto;
use App\Service\Fetcher\Interface\FetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AutoconfigureTag(name: "fetcher_provider")]
class YoutubeFetcher extends BaseFetcher implements FetcherInterface
{
    public const NAME = Providers::YOUTUBE;

    private CONST LIMIT = 50;
    /**
     * @TODO; Use http_build_query
     */
    private const PLAYLISTS_URL = 'https://youtube.googleapis.com/youtube/v3/playlists?part=contentDetails,snippet&maxResults=:limit&mine=true&key=:api_key';
    private const TRACKS_URL = 'https://youtube.googleapis.com/youtube/v3/playlistItems?';

    /**
     * @TODO: Move api key to wallet
     */
    private const API_KEY = 'AIzaSyD2-_SN8FXZj_aU8seJUzbDj1_9eNV3Hhw';

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

    private function buildUrl(): string
    {
        return strtr(self::PLAYLISTS_URL, [
            ':limit' => self::LIMIT,
            ':api_key' => self::API_KEY
        ]);
    }

    public function fetchTracks(string $playlistId): array
    {
        $items = [];
        $params = [
            'part' => 'snippet',
            'playlistId' => $playlistId,
            'maxResults' => self::LIMIT,
            'key' => self::API_KEY
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
}

