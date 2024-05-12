<?php

namespace App\Service\Fetcher;

use App\Service\Enums\Providers;
use App\Service\Fetcher\Exception\ProviderNotFoundException;
use App\Service\Fetcher\Interface\FetcherInterface;

final readonly class FetcherFactory
{
    public function __construct(private iterable $providers){}

    public function factory(Providers $provider): FetcherInterface
    {
        /** @var FetcherInterface $item */
        foreach ($this->providers as $item) {
            if ($provider->value === $item::NAME->value) {
                return $item;
            }
        }
        throw new ProviderNotFoundException(sprintf('Provider "%s" not found, or is not configured', $provider->value));
    }
}