<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Tests\Unit\Season;

use DateTimeImmutable;
use DateTimeZone;
use MoveElevator\Typo3ChameleonLogin\Season\{SeasonMatcher, SeasonStatus, SeasonStatusResolver};
use PHPUnit\Framework\Attributes\{CoversClass, Test};
use PHPUnit\Framework\TestCase;

#[CoversClass(SeasonStatusResolver::class)]
final class SeasonStatusResolverTest extends TestCase
{
    private const TIMEZONE = 'Europe/Berlin';

    #[Test]
    public function firstMatchingSeasonIsActiveAndLaterOnesAreOverridden(): void
    {
        $seasons = [
            $this->season(1, '2026-12-01', '2026-12-31'),
            $this->season(2, '2026-11-01', '2027-01-31'),
        ];

        self::assertSame(
            [1 => SeasonStatus::Active, 2 => SeasonStatus::Overridden],
            $this->resolve($seasons, '2026-12-15'),
        );
    }

    #[Test]
    public function hiddenSeasonDoesNotOverrideOthers(): void
    {
        $seasons = [
            $this->season(1, '2026-12-01', '2026-12-31', hidden: true),
            $this->season(2, '2026-12-01', '2026-12-31'),
        ];

        self::assertSame(
            [1 => SeasonStatus::Hidden, 2 => SeasonStatus::Active],
            $this->resolve($seasons, '2026-12-15'),
        );
    }

    #[Test]
    public function oneTimeSeasonIsUpcomingBeforeAndExpiredAfterItsPeriod(): void
    {
        $seasons = [$this->season(1, '2026-12-01', '2026-12-31')];

        self::assertSame([1 => SeasonStatus::Upcoming], $this->resolve($seasons, '2026-11-30'));
        self::assertSame([1 => SeasonStatus::Expired], $this->resolve($seasons, '2027-01-01'));
    }

    #[Test]
    public function recurringSeasonNeverExpires(): void
    {
        $seasons = [$this->season(1, '2026-12-01', '2026-12-31', recurring: true)];

        self::assertSame([1 => SeasonStatus::Upcoming], $this->resolve($seasons, '2030-06-01'));
    }

    /**
     * @param list<array<string, mixed>> $seasons
     *
     * @return array<int, SeasonStatus>
     */
    private function resolve(array $seasons, string $today): array
    {
        return new SeasonStatusResolver(new SeasonMatcher())->resolve($seasons, $this->day($today));
    }

    /**
     * @return array<string, mixed>
     */
    private function season(int $uid, string $from, string $until, bool $recurring = false, bool $hidden = false): array
    {
        return [
            'uid' => $uid,
            'valid_from' => $this->day($from)->getTimestamp(),
            'valid_until' => $this->day($until)->getTimestamp(),
            'recurring' => (int) $recurring,
            'hidden' => (int) $hidden,
        ];
    }

    private function day(string $date): DateTimeImmutable
    {
        return new DateTimeImmutable($date, new DateTimeZone(self::TIMEZONE));
    }
}
