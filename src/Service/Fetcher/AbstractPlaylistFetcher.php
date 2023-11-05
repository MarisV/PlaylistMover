<?php

namespace App\Service\Fetcher;

use App\Service\Fetcher\Interface\FetcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractPlaylistFetcher implements FetcherInterface {

    public function __construct(
        protected HttpClientInterface $httpClient,
        protected Security $security
    ) {

    }

    public function fetch() {}

}