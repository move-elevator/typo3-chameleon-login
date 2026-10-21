<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Tests\Unit\Style;

use MoveElevator\Typo3ChameleonLogin\Style\LoginStyles;
use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Test};
use PHPUnit\Framework\TestCase;

#[CoversClass(LoginStyles::class)]
final class LoginStylesTest extends TestCase
{
    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function colorProvider(): iterable
    {
        yield 'short hex' => ['#fff', true];
        yield 'long hex' => ['#386ac8', true];
        yield 'hex with alpha' => ['#386ac880', true];
        yield 'missing hash' => ['386ac8', false];
        yield 'named color' => ['red', false];
        yield 'css injection' => ['#fff; } body { display: none', false];
        yield 'empty' => ['', false];
    }

    #[Test]
    #[DataProvider('colorProvider')]
    public function validatesHexColors(string $color, bool $expected): void
    {
        self::assertSame($expected, LoginStyles::isValidColor($color));
    }

    #[Test]
    public function gradientNeedsAtLeastTwoValidColors(): void
    {
        self::assertSame('', LoginStyles::gradientVariables(['#fff', 'red', ''], 'slow'));
    }

    #[Test]
    public function gradientSkipsInvalidColorsAndUsesSpeed(): void
    {
        self::assertSame(
            ':root { --chameleon-login-gradient: linear-gradient(135deg, #ff0000, #00ff00); --chameleon-login-gradient-duration: 10s; }',
            LoginStyles::gradientVariables(['#ff0000', 'url(x)', '#00ff00'], 'fast'),
        );
    }

    #[Test]
    public function gradientFallsBackToMediumSpeed(): void
    {
        self::assertStringContainsString(
            '--chameleon-login-gradient-duration: 20s;',
            LoginStyles::gradientVariables(['#000', '#fff'], 'unknown'),
        );
    }
}
