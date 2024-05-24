<?php

namespace App\Service\Fetcher;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class BaseFetcher {

    protected UserInterface $user;

    public function __construct(
        protected HttpClientInterface $httpClient,
        protected Security $security
    ) {
        $this->user = $security->getUser();
    }

    protected function makeRequest(string $url): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->user->getUserOAuthByProviderKey(static::NAME->value)->getAccessToken(),
                ],
            ]
        );
    }
}