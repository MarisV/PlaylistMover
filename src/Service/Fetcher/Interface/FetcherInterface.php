<?php 

namespace App\Service\Fetcher\Interface;

interface FetcherInterface
{
    public const NAME = '';
    public function fetchPlaylists();
    public function fetchTracks();
}