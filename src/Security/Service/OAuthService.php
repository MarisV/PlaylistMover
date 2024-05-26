<?php

namespace App\Security\Service;

use App\Entity\UserOAuth;
use App\Service\Fetcher\SpotifyFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class OAuthService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $secrets
    ) {
        
    }

    public function refreshSpotifyToken(UserOAuth $userOAuth): bool
    {
        $payload = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $userOAuth->getRefreshToken(),
        ];


        $response = $this->httpClient->request(
            'POST',
            SpotifyFetcher::TOKEN_REFRESH_URL,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode(sprintf('%s:%s', $this->secrets->get('spotify_client_id'), $this->secrets->get('spotify_client_secret'))),
                ],
                'body' => $payload
            ]
        );

        $accessToken = $response->toArray()['access_token'];
        $userOAuth->setAccessToken($accessToken);

        $this->entityManager->flush();

        return true;
    }
}