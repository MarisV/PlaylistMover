<?php

namespace App\Scheduler\MessageHandler;

use App\Repository\UserOAuthRepository;
use App\Scheduler\Message\RefreshOAuthToken;
use App\Service\Enums\Providers;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RefreshOAuthTokenHandler
{
    public function __invoke(RefreshOAuthToken $message, UserOAuthRepository $userOAuthRepository): void
    {
        $oauths = $userOAuthRepository->findForTokenRefresh($message->provider);

    }
}
