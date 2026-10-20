<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Tests\Unit\Season;

use MoveElevator\Typo3ChameleonLogin\Season\SeasonMatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SeasonMatcher::class)]
final class SeasonMatcherTest extends TestCase
{
    private const TIMEZONE = 'Europe/Berlin';

    #[Test]
    public function seasonWithoutPeriodIsInactive(): void
    {
        $season = ['valid_from' => 0, 'valid_until' => $this->midnight('2026-12-31'), 'recurring' => 0];

        self::assertFalse(new SeasonMatcher()->isActive($season, $this->day('2026-06-01')));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function oneTimeSeasonProvider(): iterable
    {
        yield 'before start' => ['2026-11-30', false];
        yield 'first day' => ['2026-12-01', true];
        yield 'inside' => ['2026-12-15', true];
        yield 'last day' => ['2026-12-31', true];
        yield 'after end' => ['2027-01-01', false];
        yield 'same period next year' => ['2027-12-15', false];
    }

    #[Test]
    #[DataProvider('oneTimeSeasonProvider')]
    public function oneTimeSeasonIsActiveWithinItsPeriod(string $today, bool $expected): void
    {
        $season = [
            'valid_from' => $this->midnight('2026-12-01'),
            'valid_until' => $this->midnight('2026-12-31'),
            'recurring' => 0,
        ];

        self::assertSame($expected, new SeasonMatcher()->isActive($season, $this->day($today)));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function recurringSeasonProvider(): iterable
    {
        yield 'first day in a later year' => ['2030-06-01', true];
        yield 'last day in a later year' => ['2030-08-31', true];
        yield 'day before start' => ['2030-05-31', false];
        yield 'day after end' => ['2030-09-01', false];
    }

    #[Test]
    #[DataProvider('recurringSeasonProvider')]
    public function recurringSeasonIsActiveEveryYear(string $today, bool $expected): void
    {
        $season = [
            'valid_from' => $this->midnight('2026-06-01'),
            'valid_until' => $this->midnight('2026-08-31'),
            'recurring' => 1,
        ];

        self::assertSame($expected, new SeasonMatcher()->isActive($season, $this->day($today)));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function recurringSeasonAcrossNewYearProvider(): iterable
    {
        yield 'december' => ['2030-12-24', true];
        yield 'january' => ['2031-01-06', true];
        yield 'day before start' => ['2030-12-19', false];
        yield 'day after end' => ['2031-01-07', false];
        yield 'summer' => ['2030-07-01', false];
    }

    #[Test]
    #[DataProvider('recurringSeasonAcrossNewYearProvider')]
    public function recurringSeasonSpanningNewYearIsActive(string $today, bool $expected): void
    {
        $season = [
            'valid_from' => $this->midnight('2026-12-20'),
            'valid_until' => $this->midnight('2027-01-06'),
            'recurring' => 1,
        ];

        self::assertSame($expected, new SeasonMatcher()->isActive($season, $this->day($today)));
    }

    private function day(string $date): \DateTimeImmutable
    {
        return new \DateTimeImmutable($date, new \DateTimeZone(self::TIMEZONE));
    }

    private function midnight(string $date): int
    {
        return $this->day($date)->getTimestamp();
    }
}
