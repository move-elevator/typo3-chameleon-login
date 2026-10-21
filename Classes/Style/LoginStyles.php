<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Style;

use function array_filter;
use function array_values;
use function count;
use function implode;
use function is_string;
use function preg_match;
use function sprintf;

final class LoginStyles
{
    public const array GRADIENT_DURATIONS = ['slow' => 40, 'medium' => 20, 'fast' => 10];

    public static function isValidColor(string $color): bool
    {
        return 1 === preg_match('/^#[0-9a-fA-F]{3,8}$/', $color);
    }

    /**
     * @param array<mixed> $colors
     *
     * @return list<string>
     */
    public static function filterValidColors(array $colors): array
    {
        return array_values(array_filter(
            $colors,
            static fn (mixed $color): bool => is_string($color) && self::isValidColor($color),
        ));
    }

    /**
     * Returns the CSS custom properties for the animated gradient, or an empty
     * string if fewer than two valid colors are given.
     *
     * @param array<mixed> $colors
     */
    public static function gradientVariables(array $colors, string $speed): string
    {
        $validColors = self::filterValidColors($colors);
        if (count($validColors) < 2) {
            return '';
        }

        return sprintf(
            ':root { --chameleon-login-gradient: linear-gradient(135deg, %s); --chameleon-login-gradient-duration: %ds; }',
            implode(', ', $validColors),
            self::GRADIENT_DURATIONS[$speed] ?? self::GRADIENT_DURATIONS['medium'],
        );
    }
}
