<?php

namespace App\Service\Fetcher\Dto;

use Symfony\Component\Security\Core\User\UserInterface;

readonly class PlaylistDto
{
    public function __construct(
        private UserInterface $owner,
        private string        $title,
        private string        $provider,
        private ?string       $imageUri,
        private ?string       $providerUri,
        private ?string       $providerId,
        private ?array        $tracks
    ) {
        
    }


    public function getOwner(): UserInterface
    {
        return $this->owner;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getImageUri(): ?string
    {
        return $this->imageUri;
    }

    public function getProviderUri(): ?string
    {
        return $this->providerUri;
    }

    public function getProviderId(): ?string
    {
        return $this->providerId;
    }

    public function getTracks(): ?array
    {
        return $this->tracks;
    }
}