<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'playlists')]
    private ?User $owner = null;

    #[ORM\Column(length: 512)]
    private ?string $title = null;

    #[ORM\Column(length: 50)]
    private ?string $provider = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $providerUri = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $providerId = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $imageUri;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User|UserInterface|null $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProviderUri(): ?string
    {
        return $this->providerUri;
    }

    public function setProviderUri(?string $providerUri): static
    {
        $this->providerUri = $providerUri;

        return $this;
    }

    public function getProviderId(): ?string
    {
        return $this->providerId;
    }

    public function setProviderId(?string $providerID): static
    {
        $this->providerId = $providerID;

        return $this;
    }

    public function getImageUri(): ?string
    {
        return $this->imageUri;
    }

    public function setImageUri(?string $imageUri): static
    {
        $this->imageUri = $imageUri;

        return $this;
    }

}
