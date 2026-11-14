<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Tests\Unit\Settings;

use MoveElevator\Typo3ChameleonLogin\Settings\ExtensionSettings;
use PHPUnit\Framework\Attributes\{CoversClass, Test};
use PHPUnit\Framework\TestCase;

#[CoversClass(ExtensionSettings::class)]
final class ExtensionSettingsTest extends TestCase
{
    #[Test]
    public function returnsTrimmedStrings(): void
    {
        self::assertSame('#fff', new ExtensionSettings(['color' => ' #fff '])->getString('color'));
    }

    #[Test]
    public function returnsEmptyStringForMissingOrNonScalarValues(): void
    {
        $settings = new ExtensionSettings(['list' => ['#fff']]);

        self::assertSame('', $settings->getString('missing'));
        self::assertSame('', $settings->getString('list'));
    }

    #[Test]
    public function returnsArrays(): void
    {
        self::assertSame(['#fff', '#000'], new ExtensionSettings(['colors' => ['#fff', '#000']])->getArray('colors'));
    }

    #[Test]
    public function returnsEmptyArrayForMissingOrNonArrayValues(): void
    {
        $settings = new ExtensionSettings(['color' => '#fff']);

        self::assertSame([], $settings->getArray('missing'));
        self::assertSame([], $settings->getArray('color'));
    }

    #[Test]
    public function readsTheExtensionConfigurationFromGlobals(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][ExtensionSettings::KEY] = ['effect' => 'snow'];

        self::assertSame('snow', ExtensionSettings::fromGlobals()->getString('effect'));

        unset($GLOBALS['TYPO3_CONF_VARS']);
    }
}
