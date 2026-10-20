<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\EventListener;

use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Page\PageRenderer;

final readonly class AddLoginEffect
{
    public const array EFFECTS = ['snow', 'leaves', 'blossoms', 'confetti', 'hearts'];

    public function __construct(
        private PageRenderer $pageRenderer,
    ) {
    }

    #[AsEventListener('typo3-chameleon-login/login-effect')]
    public function __invoke(ModifyPageLayoutOnLoginProviderSelectionEvent $event): void
    {
        $effect = (string)($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['effect'] ?? '');
        if (!in_array($effect, self::EFFECTS, true)) {
            return;
        }

        $this->pageRenderer->getJavaScriptRenderer()->addJavaScriptModuleInstruction(
            JavaScriptModuleInstruction::create('@move-elevator/typo3-chameleon-login/login-effect.js')
                ->invoke('init', $effect)
        );
    }
}
