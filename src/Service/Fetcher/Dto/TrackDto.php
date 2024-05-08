<?php

namespace App\Service\Fetcher\Dto;

class TrackDto
{
    private ?array $artists;

    public function __construct(
        private readonly ?string $id,
        private readonly ?string $name,
        private readonly ?string $href,
        private readonly ?int   $popularity,
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