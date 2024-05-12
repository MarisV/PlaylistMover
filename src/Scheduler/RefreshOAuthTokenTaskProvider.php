<?php

namespace App\Scheduler;

use App\Scheduler\Message\RefreshOAuthToken;
use App\Service\Enums\Providers;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule(name: 'default')]
class RefreshOAuthTokenTaskProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                //@todo: Change frequency
                RecurringMessage::every('1 hour', new RefreshOAuthToken(Providers::SPOTIFY))
            );
    }
}