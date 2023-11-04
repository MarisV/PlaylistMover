<?php

namespace App\Service\Fetcher;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractPlaylistFetcher {

    public function __construct(
        protected HttpClientInterface $httpClient
    ) {
        
    }

    public function fetch() 
    {

    }

}