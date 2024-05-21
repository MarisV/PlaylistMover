<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'There is already an account with this username')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserOAuth::class, cascade: ['persist', 'remove'])]
    private Collection $userOAuths;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Playlist::class, cascade: ['persist', 'remove'])]
    private Collection $playlists;

    public function __construct()
    {
        $this->userOAuths = new ArrayCollection();
        $this->playlists = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('#%d - %s', $this->id, $this->email);
    }
    
    public static function fromOAuthResponse(UserResponseInterface $response): User
    {
        return (new self())
            ->setEmail($response->getEmail())
            ->setName($response->getNickname())
            ->setPassword(md5($response->getEmail()));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, UserOAuth>
     */
    public function getUserOAuths(): Collection
    {
        return $this->userOAuths;
    }

    public function addUserOAuth(UserOAuth $userOAuth): static
    {
        $create = false;

        if ($this->userOAuths->count() === 0) {
            $create = true;
        } else {
            $existingAuths = $this->userOAuths->filter(function (UserOAuth $auth) use ($userOAuth) {
                return $auth->getProvider() === $userOAuth->getProvider();
            })->count();

            if ($existingAuths === 0) {
                $create = true;
            }
        }

        if ($create) {
            $this->userOAuths->add($userOAuth);
            $userOAuth->setUser($this);
        }
        return $this;
    }

    public function removeUserOAuth(UserOAuth $userOAuth): self
    {
        if ($this->userOAuths->removeElement($userOAuth)) {
            // set the owning side to null (unless already changed)
            if ($userOAuth->getUser() === $this) {
                $userOAuth->setUser(null);
            }
        }

        return $this;
    }

    public function getUserOAuthByProviderKey(string $provider): UserOAuth|false
    {
        return $this->getUserOAuths()->filter(function(UserOAuth $auth) use ($provider) {
            return $auth->getProvider() === $provider;
        })->first();
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): static
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists->add($playlist);
            $playlist->setOwner($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): static
    {
        if ($this->playlists->removeElement($playlist)) {
            // set the owning side to null (unless already changed)
            if ($playlist->getOwner() === $this) {
                $playlist->setOwner(null);
            }
        }

        return $this;
    }
}
