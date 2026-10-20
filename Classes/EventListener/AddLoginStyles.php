<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\Event\BeforeStylesheetsRenderingEvent;

final class AddLoginStyles
{
    #[AsEventListener('typo3-chameleon-login/login-styles')]
    public function __invoke(BeforeStylesheetsRenderingEvent $event): void
    {
        $assetCollector = $event->getAssetCollector();
        $assetCollector->addStyleSheet(
            'typo3-chameleon-login-styles',
            'EXT:typo3_chameleon_login/Resources/Public/Css/Login.css'
        );

        $color = trim((string)($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundColor'] ?? ''));
        if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $color) === 1) {
            $assetCollector->addInlineStyleSheet(
                'typo3-chameleon-login-bg-color',
                sprintf(':root { --chameleon-login-bg-color: %s; }', $color)
            );
        }
    }
}
