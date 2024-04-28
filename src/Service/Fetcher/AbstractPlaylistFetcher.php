<?php

namespace App\Service\Fetcher;

use App\Service\Fetcher\Interface\FetcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractPlaylistFetcher implements FetcherInterface {

    protected UserInterface $user;

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