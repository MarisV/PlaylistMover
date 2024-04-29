<?php

namespace App\Scheduler\MessageHandler;

use App\Scheduler\Message\RefreshSpotifyToken;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RefreshSpotifyTokenHandler
{
    public function __invoke(RefreshSpotifyToken $message): void
    {
       echo 'Test - ' . PHP_EOL;
    }
}
