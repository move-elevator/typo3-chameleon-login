<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Season;

enum SeasonStatus: string
{
    case Active = 'active';
    case Overridden = 'overridden';
    case Upcoming = 'upcoming';
    case Expired = 'expired';
    case Hidden = 'hidden';
}
