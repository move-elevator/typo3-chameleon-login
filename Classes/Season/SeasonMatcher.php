<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Season;

use DateTimeImmutable;
use DateTimeZone;

final readonly class SeasonMatcher
{
    /**
     * @param array<string, mixed> $season
     */
    public function isActive(array $season, DateTimeImmutable $today): bool
    {
        $validFrom = (int) ($season['valid_from'] ?? 0);
        $validUntil = (int) ($season['valid_until'] ?? 0);
        if (0 === $validFrom || 0 === $validUntil) {
            return false;
        }

        if (false === (bool) ($season['recurring'] ?? false)) {
            return $today->getTimestamp() >= $validFrom
                && $today->getTimestamp() <= $validUntil;
        }

        $todayMonthDay = $today->format('m-d');
        $fromMonthDay = $this->toMonthDay($validFrom, $today->getTimezone());
        $untilMonthDay = $this->toMonthDay($validUntil, $today->getTimezone());

        return $fromMonthDay <= $untilMonthDay
            ? ($todayMonthDay >= $fromMonthDay && $todayMonthDay <= $untilMonthDay)
            : ($todayMonthDay >= $fromMonthDay || $todayMonthDay <= $untilMonthDay);
    }

    // Timestamps are stored as local midnight; formatting them in UTC would shift the day.
    private function toMonthDay(int $timestamp, DateTimeZone $timezone): string
    {
        return new DateTimeImmutable('@'.$timestamp)
            ->setTimezone($timezone)
            ->format('m-d');
    }
}
