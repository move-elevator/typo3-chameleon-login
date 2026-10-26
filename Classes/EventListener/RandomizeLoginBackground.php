<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\EventListener;

use MoveElevator\Typo3ChameleonLogin\Style\LoginStyles;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SystemResource\Exception\SystemResourceException;
use TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface;
use TYPO3\CMS\Core\SystemResource\SystemResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_rand;
use function sprintf;

final readonly class RandomizeLoginBackground
{
    public function __construct(
        private PageRenderer $pageRenderer,
        private SystemResourceFactory $resourceFactory,
        private SystemResourcePublisherInterface $resourcePublisher,
    ) {}

    /**
     * The core has already added its background image styles when this event is dispatched,
     * so the randomly picked image and its focus position are added as later inline blocks that override them.
     */
    #[AsEventListener('typo3-chameleon-login/randomize-background')]
    public function __invoke(ModifyPageLayoutOnLoginProviderSelectionEvent $event): void
    {
        $configuration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login'] ?? [];

        $images = (array) ($configuration['loginBackgroundImages'] ?? []);
        if ([] !== $images) {
            $this->addBackgroundImage('typo3-chameleon-login-random-background', $images, $event->getRequest(), false);
        }

        $darkImages = (array) ($configuration['loginBackgroundImagesDark'] ?? []);
        if ([] !== $darkImages) {
            $this->addBackgroundImage('typo3-chameleon-login-random-background-dark', $darkImages, $event->getRequest(), true);
        }
    }

    /**
     * @param array<mixed> $images
     */
    private function addBackgroundImage(string $name, array $images, ServerRequestInterface $request, bool $darkMode): void
    {
        $image = (array) $images[array_rand($images)];
        try {
            $uri = (string) $this->resourcePublisher->generateUri(
                $this->resourceFactory->createPublicResource((string) ($image['url'] ?? '')),
                $request,
            );
        } catch (SystemResourceException) {
            return;
        }

        $css = sprintf('background-image: url("%s");', GeneralUtility::sanitizeCssVariableValue($uri));
        $position = (string) ($image['position'] ?? '');
        if (LoginStyles::isValidPosition($position)) {
            $css .= sprintf(' background-position: %s;', $position);
        }
        $css = sprintf('.typo3-login { %s }', $css);
        $this->pageRenderer->addCssInlineBlock($name, $darkMode ? LoginStyles::darkMode($css) : $css, null, false, true);
    }
}
