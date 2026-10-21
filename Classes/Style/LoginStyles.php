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
        $gradient = self::gradient($colors);
        if ('' === $gradient) {
            return '';
        }

        return sprintf(
            ':root { --chameleon-login-gradient: %s; --chameleon-login-gradient-duration: %ds; }',
            $gradient,
            self::GRADIENT_DURATIONS[$speed] ?? self::GRADIENT_DURATIONS['medium'],
        );
    }

    /**
     * Returns the gradient as CSS value, or an empty string if fewer than two valid colors are given.
     *
     * @param array<mixed> $colors
     */
    public static function gradient(array $colors): string
    {
        $validColors = self::filterValidColors($colors);

        return count($validColors) < 2 ? '' : sprintf('linear-gradient(135deg, %s)', implode(', ', $validColors));
    }

    /**
     * Mirrors the variables the core sets for `loginHighlightColor`, so a dark mode color can override them.
     */
    public static function highlightColorStyles(string $color): string
    {
        if (false === self::isValidColor($color)) {
            return '';
        }

        return sprintf(
            '.typo3-login { --typo3-login-highlight: %1$s; }'
            .' .btn-login {'
            .' --typo3-btn-bg: %1$s; --typo3-btn-border-color: hsl(from %1$s h s calc(l - 5));'
            .' --typo3-btn-hover-bg: hsl(from %1$s h s calc(l - 3)); --typo3-btn-hover-border-color: hsl(from %1$s h s calc(l - 8));'
            .' --typo3-btn-focus-bg: hsl(from %1$s h s calc(l - 6)); --typo3-btn-focus-border-color: hsl(from %1$s h s calc(l - 11));'
            .' --typo3-btn-disabled-bg: %1$s; --typo3-btn-disabled-border-color: hsl(from %1$s h s calc(l - 5));'
            .' --typo3-btn-color: #fff; --typo3-btn-hover-color: #fff; --typo3-btn-focus-color: #fff; --typo3-btn-disabled-color: #fff; }'
            .' .card-login .card-footer { border-color: %1$s; }',
            $color,
        );
    }

    public static function darkMode(string $css): string
    {
        return '' === $css ? '' : sprintf('@media (prefers-color-scheme: dark) { %s }', $css);
    }
}
