<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Controller;

use DateTimeImmutable;
use MoveElevator\Typo3ChameleonLogin\Season\{SeasonRepository, SeasonStatusResolver};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ComponentFactory;
use TYPO3\CMS\Backend\Template\{ModuleTemplate, ModuleTemplateFactory};
use TYPO3\CMS\Core\Imaging\{IconFactory, IconSize};
use TYPO3\CMS\Core\Localization\LanguageService;

use function array_map;

#[AsController]
final readonly class SeasonModuleController
{
    public const string MODULE = 'site_login_seasons';

    public function __construct(
        private ModuleTemplateFactory $moduleTemplateFactory,
        private ComponentFactory $componentFactory,
        private UriBuilder $uriBuilder,
        private IconFactory $iconFactory,
        private SeasonRepository $seasonRepository,
        private SeasonStatusResolver $seasonStatusResolver,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);
        $this->addNewSeasonButton($view);

        $seasons = $this->seasonRepository->findAll();
        $statuses = $this->seasonStatusResolver->resolve($seasons, new DateTimeImmutable('today'));

        return $view->assignMultiple([
            'table' => SeasonRepository::TABLE,
            'seasons' => array_map(
                static fn (array $season): array => [...$season, 'status' => $statuses[(int) $season['uid']]->value],
                $seasons,
            ),
        ])->renderResponse('SeasonModule/Overview');
    }

    private function addNewSeasonButton(ModuleTemplate $view): void
    {
        $button = $this->componentFactory->createLinkButton()
            ->setHref((string) $this->uriBuilder->buildUriFromRoute('record_edit', [
                'edit' => [SeasonRepository::TABLE => [0 => 'new']],
                'module' => self::MODULE,
                'returnUrl' => (string) $this->uriBuilder->buildUriFromRoute(self::MODULE),
            ]))
            ->setShowLabelText(true)
            ->setTitle($this->getLanguageService()->sL('typo3_chameleon_login.module:season.new'))
            ->setIcon($this->iconFactory->getIcon('actions-plus', IconSize::SMALL));
        $view->getDocHeaderComponent()->getButtonBar()->addButton($button);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
