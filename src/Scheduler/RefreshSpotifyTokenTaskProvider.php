<?php

namespace App\Scheduler;

use App\Scheduler\Message\RefreshSpotifyToken;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule(name: 'default')]
class RefreshSpotifyTokenTaskProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every('1 minute', new RefreshSpotifyToken())
            );
    }
}