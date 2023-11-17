<?php

namespace App\Service\Fetcher;

use App\Entity\User;
use App\Service\Fetcher\Interface\FetcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractPlaylistFetcher implements FetcherInterface {

    protected User $user;

    public function __construct(
        protected HttpClientInterface $httpClient,
        protected Security $security
    ) {
        $this->user = $security->getUser();
    }

    protected function emptyResponse()
    {
        return new JsonResponse([
            'count' => 0,
            'data' => [],
        ]);
    }
}