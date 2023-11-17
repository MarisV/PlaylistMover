<?php 

namespace App\Service\Fetcher\Interface;

interface FetcherInterface
{
    public function fetchPlaylists();
    public function fetchTracks();
}