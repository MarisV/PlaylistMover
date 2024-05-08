<?php

namespace App\Service\Fetcher\Dto;

readonly class ArtistDto
{
    public function __construct(
        private ?string $id,
        private ?string $name,
        private ?string $href
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
}