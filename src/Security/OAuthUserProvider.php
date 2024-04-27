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

readonly class OAuthUserProvider implements AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    public function __construct(
        private UserRepository         $userRepository,
        private UserOAuthRepository    $userAuthRepository,
        private EntityManagerInterface $em,
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
            $oauth
                ->setAccessToken($response->getAccessToken())
                ->setRefreshToken($response->getRefreshToken());
            $this->em->flush();
        }

        if (null !== $response->getEmail()) {
            $user = $this->userRepository->findOneByEmail($response->getEmail()); // todo: can search user by oauth properties in case multiple oAuths have different emails
            if (null !== $user) {
                return $user;
            } else {
                return $this->createUserByOAuthUserResponse($response);
            }
        }

        throw new Exception('Email is null or not provided');

    }

    private function createUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $newUser = (new User())
            ->setEmail($response->getEmail())
            ->setName($response->getNickname())
            ->setPassword(md5($response->getEmail()));

        $this->em->persist($newUser);

        $newUser->addUserOAuth($this->createOauthEntry($response));

        $this->em->flush();

        return $newUser;
    }


    private function updateUserByOAuthUserResponse(UserInterface $user, UserResponseInterface $response): UserInterface
    {
        /** @var User $user */
        $oauth = $this->createOauthEntry($response);

        $user->addUserOAuth($oauth);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function createOauthEntry(UserResponseInterface $response): UserOAuth
    {
        $oauth = new UserOAuth();
        $oauth->setIdentifier($response->getEmail());
        $oauth->setProvider($response->getResourceOwner()->getName());
        $oauth->setAccessToken($response->getAccessToken());
        $oauth->setRefreshToken($response->getRefreshToken());
        $oauth->setUsername($response->getUsername());

        $this->em->persist($oauth);

        return $oauth;
    }
}