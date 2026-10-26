<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Season;

use DateTimeImmutable;

final readonly class SeasonStatusResolver
{
    public function __construct(
        private SeasonMatcher $seasonMatcher,
    ) {}

    /**
     * @param list<array<string, mixed>> $seasons
     *
     * @return array<int, SeasonStatus>
     */
    public function resolve(array $seasons, DateTimeImmutable $today): array
    {
        $statuses = [];
        $activeSeasonFound = false;
        foreach ($seasons as $season) {
            $status = $this->resolveIgnoringPrecedence($season, $today);
            if (SeasonStatus::Active === $status) {
                $status = $activeSeasonFound ? SeasonStatus::Overridden : SeasonStatus::Active;
                $activeSeasonFound = true;
            }
            $statuses[(int) $season['uid']] = $status;
        }

        return $statuses;
    }

    /**
     * @param array<string, mixed> $season
     */
    private function resolveIgnoringPrecedence(array $season, DateTimeImmutable $today): SeasonStatus
    {
        return match (true) {
            (bool) ($season['hidden'] ?? false) => SeasonStatus::Hidden,
            $this->seasonMatcher->isActive($season, $today) => SeasonStatus::Active,
            $this->isExpired($season, $today) => SeasonStatus::Expired,
            default => SeasonStatus::Upcoming,
        };
    }

    /**
     * @param array<string, mixed> $season
     */
    private function isExpired(array $season, DateTimeImmutable $today): bool
    {
        $validUntil = (int) ($season['valid_until'] ?? 0);

        return !(bool) ($season['recurring'] ?? false)
            && 0 !== $validUntil
            && $today->getTimestamp() > $validUntil;
    }
}
