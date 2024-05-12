<?php

namespace App\Service\Fetcher\Dto;

final readonly class TrackDto
{
    private ?array $artists;

    public function __construct(
        private ?string          $id,
        private ?string          $name,
        private ?string          $href,
        private ?int             $popularity,
    )
    {
        
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function getPopularity(): ?int
    {
        return $this->popularity;
    }

    public function getArtists(): ?array
    {
        return $this->artists;
    }

    public function addArtist(ArtistDto $artistDto): static
    {
        $this->artists[] = $artistDto;
        return $this;
    }
}