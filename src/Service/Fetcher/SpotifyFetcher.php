<?php 

namespace App\Service\Fetcher;

use App\Service\Enums\Providers;
use App\Service\Fetcher\Interface\FetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AutoconfigureTag(name: "fetcher_provider")]
class SpotifyFetcher extends BaseFetcher implements FetcherInterface
{   
    private CONST LIMIT = 50;
    private const FETCH_URL = 'https://api.spotify.com/v1/users/:user_id/playlists?offset=0&limit=:limit';

    public const NAME = Providers::SPOTIFY;

    final function fetchPlaylists(): JsonResponse|array
    {
        $items = [];
        $url = $this->buildUrl();
  
        do {
            $response = $this->makeRequest($url);
            $responseData = $response->toArray(); 

            $currentItems = $this->getItemsFromResponse($responseData);
            $items = array_merge($items, $currentItems);

            $url = $this->getNextUrl($responseData);

        } while ($url);

        if (empty($items)) {
            return $this->emptyResponse();
        }

         
        foreach($items as $item) {
            $playList = [];

            $playList['id'] = $item['id'];
            $playList['title'] = $item['name'];
            $playList['image'] = end($item['images'])['url'];
            $playList['tracks'] = $item['tracks']['total'];

            $data[] = $playList;
        }
          
        return [
            'count' => count($data),
            'data' => $data
        ];
    }


    public function fetchTracks()
    {
    }

    private function buildUrl(): string
    {
        $auth = $this->user->getUserOAuthByProviderKey(self::NAME->value);

        return strtr(self::FETCH_URL, [
            ':user_id' => $auth->getUsername(),
            ':limit' => self::LIMIT,
        ]);
    }

    private function makeRequest(string $url): ResponseInterface
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                $url,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->user->getUserOAuthByProviderKey(self::NAME->value)->getAccessToken(),
                    ],
                ]
            );
        } catch (\Exception $exception) {
            throw $exception;
        }

        return $response;
    }

    private function getNextUrl(array $responseData): ?string
    {
        return $responseData['next'] ?? null;
    }

    private function getItemsFromResponse(array $responseData): array
    {
        return $responseData['items'] ?? [];
    }

}