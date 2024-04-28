<?php

namespace App\Security;

use App\Security\Exception\ConnectionException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use App\Repository\UserRepository;
use App\Repository\UserOAuthRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use App\Entity\UserOAuth;
use App\Entity\User;

readonly class OAuthUserProvider implements AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    public function __construct(
        private UserRepository         $userRepository,
        private UserOAuthRepository    $userAuthRepository,
        private EntityManagerInterface $em,
        private Security               $security,
    )
    {
    }

    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        $this->updateUserByOAuthUserResponse($user, $response);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $oauth = $this->userAuthRepository->findOneBy([
            'provider' => $response->getResourceOwner()->getName(),
            'identifier' => $response->getEmail(),
        ]);

        if ($oauth instanceof UserOAuth) {
            $oauth
                ->setAccessToken($response->getAccessToken())
                ->setRefreshToken($response->getRefreshToken());
            $this->em->flush();

            return $oauth->getUser();
        }

        if ($response->getEmail() !== null) {
            if ($currentUser !== null) {
                $connection = $currentUser->getUserOAuthByProviderKey($response->getResourceOwner()->getName());
                if (!$connection) {
                    return $this->updateUserByOAuthUserResponse($currentUser, $response);
                }
            }

            $user = $this->userRepository->findOneBy([
                'email' => $response->getEmail()
            ]);

            return $user ?? $this->createUserByOAuthUserResponse($response);
        }

        throw new ConnectionException('Email is null or not provided');
    }

    private function createUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $user = User::fromOAuthResponse($response);
        $oauth = UserOAuth::fromOAuthResponse($response);

        $user->addUserOAuth($oauth);

        $this->em->persist($user);
        $this->em->persist($oauth);
        $this->em->flush();

        return $user;
    }


    private function updateUserByOAuthUserResponse(UserInterface $user, UserResponseInterface $response): UserInterface
    {
        /** @var User $user */
        $oauth = UserOAuth::fromOAuthResponse($response);

        $user->addUserOAuth($oauth);
        $this->em->persist($oauth);
        $this->em->flush();

        return $user;
    }
}