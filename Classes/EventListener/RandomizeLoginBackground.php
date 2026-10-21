<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\EventListener;

use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SystemResource\Exception\SystemResourceException;
use TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface;
use TYPO3\CMS\Core\SystemResource\SystemResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_rand;
use function count;
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
     * so the randomly picked image is added as a later inline block that overrides them.
     */
    #[AsEventListener('typo3-chameleon-login/randomize-background')]
    public function __invoke(ModifyPageLayoutOnLoginProviderSelectionEvent $event): void
    {
        $images = (array) ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundImages'] ?? []);
        if (count($images) < 2) {
            return;
        }

        try {
            $uri = (string) $this->resourcePublisher->generateUri(
                $this->resourceFactory->createPublicResource((string) $images[array_rand($images)]),
                $event->getRequest(),
            );
        } catch (SystemResourceException) {
            return;
        }

        $this->pageRenderer->addCssInlineBlock(
            'typo3-chameleon-login-random-background',
            sprintf('.typo3-login { background-image: url("%s"); }', GeneralUtility::sanitizeCssVariableValue($uri)),
            null,
            false,
            true,
        );
    }
}
