<?php

declare(strict_types=1);

\defined('TYPO3') || exit;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['typo3_chameleon_login']
    = MoveElevator\Typo3ChameleonLogin\Hook\DataHandlerHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['typo3_chameleon_login']
    = MoveElevator\Typo3ChameleonLogin\Hook\DataHandlerHook::class;

(static function (): void {
    $generated = TYPO3\CMS\Core\Core\Environment::getConfigPath().'/system/typo3_chameleon_login.generated.php';
    if (false === \is_file($generated)) {
        return;
    }

    $applied = require $generated;
    if (\is_array($applied)) {
        $GLOBALS['TYPO3_CONF_VARS'] = \array_replace_recursive(
            $GLOBALS['TYPO3_CONF_VARS'],
            $applied,
        );
    }
})();
