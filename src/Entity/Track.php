<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use App\Service\Fetcher\Dto\TrackDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TrackRepository::class)]
class Track
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $href = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $popularity = null;

    #[ORM\ManyToMany(targetEntity: Playlist::class, mappedBy: 'tracks')]
    private Collection $playlists;

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'tracks')]
    private Collection $artists;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hash = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $isrc = null;

    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->artists = new ArrayCollection();
    }

    public static function fromDTO(TrackDto $dto): Track
    {
        return (new self)
            ->setName($dto->getName())
            ->setHref($dto->getHref())
            ->setPopularity($dto->getPopularity());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(?string $href): static
    {
        $this->href = $href;

        return $this;
    }

    public function getPopularity(): ?int
    {
        return $this->popularity;
    }

    public function setPopularity(?int $popularity): static
    {
        $this->popularity = $popularity;

        return $this;
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
            $playlist->addTrack($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): static
    {
        if ($this->playlists->removeElement($playlist)) {
            $playlist->removeTrack($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): static
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
        }

        return $this;
    }

    public function removeArtist(Artist $artist): static
    {
        $this->artists->removeElement($artist);

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getIsrc(): ?string
    {
        return $this->isrc;
    }

    public function setIsrc(?string $isrc): static
    {
        $this->isrc = $isrc;

        return $this;
    }
}
