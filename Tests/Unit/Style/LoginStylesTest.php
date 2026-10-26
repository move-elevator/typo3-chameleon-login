<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Tests\Unit\Style;

use MoveElevator\Typo3ChameleonLogin\Style\LoginStyles;
use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Test};
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;

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

    #[Test]
    public function darkModeWrapsCssInMediaQuery(): void
    {
        self::assertSame(
            '@media (prefers-color-scheme: dark) { :root { --x: 1; } }',
            LoginStyles::darkMode(':root { --x: 1; }'),
        );
    }

    #[Test]
    public function darkModeSkipsEmptyCss(): void
    {
        self::assertSame('', LoginStyles::darkMode(''));
    }

    #[Test]
    public function highlightColorStylesSetLoginAndButtonVariables(): void
    {
        $css = LoginStyles::highlightColorStyles('#c00');

        self::assertStringContainsString('--typo3-login-highlight: #c00;', $css);
        self::assertStringContainsString('--typo3-btn-bg: #c00;', $css);
    }

    #[Test]
    public function highlightColorStylesRejectInvalidColors(): void
    {
        self::assertSame('', LoginStyles::highlightColorStyles('#fff; } body { display: none'));
    }

    /**
     * @return iterable<string, array{Area, string}>
     */
    public static function focusAreaProvider(): iterable
    {
        yield 'centered' => [new Area(1 / 3, 1 / 3, 1 / 3, 1 / 3), '50% 50%'];
        yield 'top left corner' => [new Area(0.0, 0.0, 0.25, 0.2), '0% 0%'];
        yield 'bottom right corner' => [new Area(0.75, 0.8, 0.25, 0.2), '100% 100%'];
        yield 'in between' => [new Area(0.1, 0.3, 0.2, 0.4), '12.5% 50%'];
        yield 'full width' => [new Area(0.0, 0.1, 1.0, 0.2), '50% 12.5%'];
    }

    #[Test]
    #[DataProvider('focusAreaProvider')]
    public function backgroundPositionKeepsTheFocusAreaInView(Area $focusArea, string $expected): void
    {
        self::assertSame($expected, LoginStyles::backgroundPosition($focusArea));
    }

    #[Test]
    public function backgroundPositionIsEmptyWithoutFocusArea(): void
    {
        self::assertSame('', LoginStyles::backgroundPosition(Area::createEmpty()));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function positionProvider(): iterable
    {
        yield 'integers' => ['20% 75%', true];
        yield 'decimals' => ['50% 33.33%', true];
        yield 'keywords' => ['left bottom', false];
        yield 'css injection' => ['0% 0%; } body { display: none', false];
        yield 'empty' => ['', false];
    }

    #[Test]
    #[DataProvider('positionProvider')]
    public function validatesBackgroundPositions(string $position, bool $expected): void
    {
        self::assertSame($expected, LoginStyles::isValidPosition($position));
    }
}
