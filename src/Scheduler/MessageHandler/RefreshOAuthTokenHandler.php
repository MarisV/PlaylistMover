<?php

namespace App\Scheduler\MessageHandler;

use App\Repository\UserOAuthRepository;
use App\Scheduler\Message\RefreshOAuthToken;
use App\Security\Service\OAuthService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RefreshOAuthTokenHandler
{
    public function __invoke(
        RefreshOAuthToken $message,
        UserOAuthRepository $userOAuthRepository,
        OAuthService $oAuthService,
        LoggerInterface $logger
    ): void {
        $oauths = $userOAuthRepository->findForTokenRefresh($message->provider);

        foreach ($oauths as $oauth) {
            $oAuthService->refreshSpotifyToken($oauth);
            $logger->info(sprintf('Updated access-token: %s', $oauth));
        }

    }
}
