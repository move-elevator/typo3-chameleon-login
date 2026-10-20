<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\EventListener;

use MoveElevator\Typo3ChameleonLogin\Style\LoginStyles;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\Event\BeforeStylesheetsRenderingEvent;

use function sprintf;
use function trim;

final class AddLoginStyles
{
    #[AsEventListener('typo3-chameleon-login/login-styles')]
    public function __invoke(BeforeStylesheetsRenderingEvent $event): void
    {
        $configuration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login'] ?? [];
        $assetCollector = $event->getAssetCollector();
        $assetCollector->addStyleSheet(
            'typo3-chameleon-login-styles',
            'EXT:typo3_chameleon_login/Resources/Public/Css/Login.css'
        );

        $color = trim((string)($configuration['loginBackgroundColor'] ?? ''));
        if (LoginStyles::isValidColor($color)) {
            $assetCollector->addInlineStyleSheet(
                'typo3-chameleon-login-bg-color',
                sprintf(':root { --chameleon-login-bg-color: %s; }', $color)
            );
        }

        $gradientVariables = LoginStyles::gradientVariables(
            (array)($configuration['loginBackgroundGradient'] ?? []),
            (string)($configuration['loginBackgroundGradientSpeed'] ?? '')
        );
        if ('' !== $gradientVariables) {
            $assetCollector->addStyleSheet(
                'typo3-chameleon-login-gradient',
                'EXT:typo3_chameleon_login/Resources/Public/Css/Gradient.css'
            );
            $assetCollector->addInlineStyleSheet('typo3-chameleon-login-gradient-variables', $gradientVariables);
        }
    }
}
