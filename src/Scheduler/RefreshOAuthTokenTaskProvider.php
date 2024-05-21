<?php

namespace App\Scheduler;

use App\Scheduler\Message\RefreshOAuthToken;
use App\Service\Enums\Providers;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule(name: 'default')]
readonly class RefreshOAuthTokenTaskProvider implements ScheduleProviderInterface
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {

    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every(
                    $this->parameterBag->get('spotify_token_refresh_frequency'),
                    new RefreshOAuthToken(Providers::SPOTIFY)
                )
            );
    }
}