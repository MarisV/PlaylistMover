<?php

namespace App\Service\Fetcher;

use App\Service\Enums\Providers;
use App\Service\Fetcher\Interface\FetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AutoconfigureTag(name: "fetcher_provider")]
class YoutubeFetcher extends BaseFetcher implements FetcherInterface
{
    public const NAME = Providers::YOUTUBE;

    private CONST LIMIT = 50;
    private const FETCH_URL = 'https://youtube.googleapis.com/youtube/v3/playlists?part=contentDetails,snippet&maxResults=:limit&mine=true&key=:api_key';
    private const API_KEY = 'AIzaSyD2-_SN8FXZj_aU8seJUzbDj1_9eNV3Hhw';

    public function fetchPlaylistsData(): array
    {

        $items = [];
        $url = $this->buildUrl();

        $response = $this->makeRequest($url);
        $responseData = $response->toArray();

        return [
            'count' => 0,
            'data' => []
        ];
    }

    private function buildUrl(): string
    {
        $auth = $this->user->getUserOAuthByProviderKey(self::NAME->value);

        return strtr(self::FETCH_URL, [
            ':limit' => self::LIMIT,
            ':api_key' => self::API_KEY
        ]);
    }

    private function makeRequest(string $url): ResponseInterface
    {

        return $this->httpClient->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->user->getUserOAuthByProviderKey(self::NAME->value)->getAccessToken(),
                ],
            ]
        );

    }

    public function fetchTracks()
    {
        // TODO: Implement fetchTracks() method.
    }
}