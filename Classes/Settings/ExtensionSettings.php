<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Settings;

use function is_array;
use function is_scalar;
use function trim;

/**
 * Read at call time, because ApplyActiveSeason merges the active season into TYPO3_CONF_VARS during the request.
 */
final readonly class ExtensionSettings
{
    public const string KEY = 'typo3_chameleon_login';

    /**
     * @param array<mixed> $settings
     */
    public function __construct(
        private array $settings,
    ) {}

    public static function fromGlobals(): self
    {
        return new self((array) ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][self::KEY] ?? []));
    }

    public function getString(string $key): string
    {
        $value = $this->settings[$key] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /**
     * @return array<mixed>
     */
    public function getArray(string $key): array
    {
        $value = $this->settings[$key] ?? [];

        return is_array($value) ? $value : [];
    }
}
