<?php 

namespace App\Security;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use App\Repository\UserRepository;
use App\Repository\UserOAuthRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use App\Entity\UserOAuth;
use App\Entity\User;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class OAuthUserProvider implements AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    public function __construct(
        private UserRepository         $userRepository,
        private UserOAuthRepository    $userAuthRepository,
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private Security $security,
    ) {

    }

    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        $this->updateUserByOAuthUserResponse($user, $response);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {

        $oauth = $this->userAuthRepository->findOneBy([
            'provider' => $response->getResourceOwner()->getName(),
            'identifier' => $response->getEmail(),
        ]);

        if ($oauth instanceof UserOAuth) {
            return $oauth->getUser();
        }

        if (null !== $response->getEmail()) {
            $user = $this->userRepository->findOneByEmail($response->getEmail());
            if (null !== $user) {
                return $this->updateUserByOAuthUserResponse($user, $response);
            }

            return $this->createUserByOAuthUserResponse($response);
        }

        throw new Exception('Email is null or not provided');

    }

    private function createUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
         /** @var User $user */
        $user = $this->security->getUser();
        
        return $this->updateUserByOAuthUserResponse($user, $response);
    }


    private function updateUserByOAuthUserResponse(UserInterface $user, UserResponseInterface $response): UserInterface
    {
        /** @var User $user */

        $oauth = new UserOAuth();
        $oauth->setIdentifier($response->getEmail());
        $oauth->setProvider($response->getResourceOwner()->getName());
        $oauth->setAccessToken($response->getAccessToken());
        $oauth->setRefreshToken($response->getRefreshToken());
        $oauth->setUsername($response->getUsername());

        $user->addUserOAuth($oauth);
        $this->em->persist($user);
        $this->em->persist($oauth);
        $this->em->flush();

        return $user;
    }
}