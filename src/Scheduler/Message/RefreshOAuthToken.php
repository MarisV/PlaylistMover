<?php

namespace App\Scheduler\Message;

use App\Service\Enums\Providers;

final readonly class RefreshOAuthToken
{
    public function __construct(public Providers $provider){}

    public function __toString() {
        return sprintf('Refreshing expired "%s" oauth token', $this->provider->value);
    }
}
