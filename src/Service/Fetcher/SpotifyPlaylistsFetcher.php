<?php 

namespace App\Service\Fetcher;

use Exception;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpotifyPlaylistsFetcher extends AbstractPlaylistFetcher 
{   
    private const PROVIDER = 'spotify';
    private CONST LIMIT = 50;

    private const FETCH_URL = 'https://api.spotify.com/v1/users/:user_id/playlists?offset=0&limit=:limit';

    final function fetchPlaylists()
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
        $auth = $this->user->getUserOAuthByProviderKey(self::PROVIDER);

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
                        'Authorization' => 'Bearer ' . $this->user->getUserOAuthByProviderKey(self::PROVIDER)->getAccessToken(),
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