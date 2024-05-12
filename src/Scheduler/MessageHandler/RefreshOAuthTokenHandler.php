<?php

namespace App\Scheduler\MessageHandler;

use App\Repository\UserOAuthRepository;
use App\Scheduler\Message\RefreshOAuthToken;
use App\Security\Service\OAuthService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RefreshOAuthTokenHandler
{
    public function __construct(
        private UserOAuthRepository $userOAuthRepository,
        private OAuthService        $oAuthService,
        private LoggerInterface     $logger
    ) {}

    public function __invoke(RefreshOAuthToken $message): void {
        $oauths = $this->userOAuthRepository->findForTokenRefresh($message->provider);

        foreach ($oauths as $oauth) {
            $this->oAuthService->refreshSpotifyToken($oauth);
            $this->logger->info(sprintf('Updated access-token: %s', $oauth));
        }
    }
}
