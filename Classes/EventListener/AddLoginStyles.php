<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\EventListener;

use MoveElevator\Typo3ChameleonLogin\Routing\LoginRoute;
use MoveElevator\Typo3ChameleonLogin\Settings\ExtensionSettings;
use MoveElevator\Typo3ChameleonLogin\Style\LoginStyles;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\Event\BeforeStylesheetsRenderingEvent;

use function sprintf;

final class AddLoginStyles
{
    /**
     * This event is dispatched for every page. The core's login layout event would avoid the route check,
     * but the password reset and MFA screens don't dispatch it.
     */
    #[AsEventListener('typo3-chameleon-login/login-styles')]
    public function __invoke(BeforeStylesheetsRenderingEvent $event): void
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface || false === LoginRoute::matches($request)) {
            return;
        }

        $settings = ExtensionSettings::fromGlobals();
        $assetCollector = $event->getAssetCollector();
        $assetCollector->addStyleSheet(
            'typo3-chameleon-login-styles',
            'EXT:typo3_chameleon_login/Resources/Public/Css/Login.css',
        );

        $color = $settings->getString('loginBackgroundColor');
        if (LoginStyles::isValidColor($color)) {
            $assetCollector->addInlineStyleSheet(
                'typo3-chameleon-login-bg-color',
                sprintf(':root { --chameleon-login-bg-color: %s; }', $color),
            );
        }
        $darkColor = $settings->getString('loginBackgroundColorDark');
        if (LoginStyles::isValidColor($darkColor)) {
            $assetCollector->addInlineStyleSheet(
                'typo3-chameleon-login-bg-color-dark',
                LoginStyles::darkMode(sprintf(':root { --chameleon-login-bg-color: %s; }', $darkColor)),
            );
        }

        // Rendered after the core's highlight color block, so it overrides it in dark mode
        $darkHighlightColor = LoginStyles::highlightColorStyles($settings->getString('loginHighlightColorDark'));
        if ('' !== $darkHighlightColor) {
            $assetCollector->addInlineStyleSheet(
                'typo3-chameleon-login-highlight-color-dark',
                LoginStyles::darkMode($darkHighlightColor),
            );
        }

        $gradientVariables = LoginStyles::gradientVariables(
            $settings->getArray('loginBackgroundGradient'),
            $settings->getString('loginBackgroundGradientSpeed'),
        );
        if ('' !== $gradientVariables) {
            $assetCollector->addStyleSheet(
                'typo3-chameleon-login-gradient',
                'EXT:typo3_chameleon_login/Resources/Public/Css/Gradient.css',
            );
            $assetCollector->addInlineStyleSheet('typo3-chameleon-login-gradient-variables', $gradientVariables);

            $darkGradient = LoginStyles::gradient($settings->getArray('loginBackgroundGradientDark'));
            if ('' !== $darkGradient) {
                $assetCollector->addInlineStyleSheet(
                    'typo3-chameleon-login-gradient-variables-dark',
                    LoginStyles::darkMode(sprintf(':root { --chameleon-login-gradient: %s; }', $darkGradient)),
                );
            }
        }
    }
}
