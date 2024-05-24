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
class SpotifyFetcher extends BaseFetcher implements FetcherInterface
{
    public const NAME = Providers::SPOTIFY;

    private CONST LIMIT = 50;
    private const FETCH_URL = 'https://api.spotify.com/v1/users/:user_id/playlists?offset=0&limit=:limit';
    public const TOKEN_REFRESH_URL = 'https://accounts.spotify.com/api/token';


    final function fetchPlaylistsData(): array
    {
        $items = [];
        $url = $this->buildUrl();
  
        do {
            $response = $this->makeRequest($url);
            $responseData = $response->toArray(); 

            $currentItems = $responseData['items'] ?? [];
            $items = array_merge($items, $currentItems);

            $url = $responseData['next'] ?? null;

        } while ($url);

        $data = [];

        foreach($items as $item) {
            $playList = new PlaylistDto(
                $this->user,
                $item['name'],
                self::NAME->value,
                $item['images'] ? end($item['images'])['url'] : '-',
                $item['uri'],
                $item['id'],
                $this->fetchTracks($item['tracks'])
            );

            $data[] = $playList;
        }
          
        return [
            'count' => count($data),
            'data' => $data
        ];
    }


    public function fetchTracks(array $playlist): array
    {
        $items = [];
        $url = $playlist['href'];

        $url .= '?fields=next,items(track(id,name,popularity,href,external_ids(isrc),artists(id, name,uri)))';
        do {
            $response = $this->makeRequest($url)->getContent();

            $responseData = json_decode($response, true);

            $currentItems = $responseData['items'] ?? [];
            $items = array_merge($items, $currentItems);

            $url = $responseData['next'] ?? null;
        } while ($url);

        $tracks = [];
        foreach ($items as $row) {
            $item = $row['track'];
            $track = new TrackDto(
                $item['id'],
                $item['name'],
                $item['href'],
                $item['popularity'],
                $item['external_ids']['isrc'] ?? (md5($item['name'] ?? $item['href']))
            );

            foreach ($item['artists'] as $artist) {
                $track->addArtist(
                    new ArtistDto(
                        $artist['id'],
                        $artist['name'],
                        $artist['uri'],
                    )
                );
            }
            $tracks[] = $track;
        }

        return $tracks;
    }

    private function buildUrl(): string
    {
        $auth = $this->user->getUserOAuthByProviderKey(self::NAME->value);

        return strtr(self::FETCH_URL, [
            ':user_id' => $auth->getUsername(),
            ':limit' => self::LIMIT,
        ]);
    }
}