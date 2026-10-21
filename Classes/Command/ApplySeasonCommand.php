<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Command;

use MoveElevator\Typo3ChameleonLogin\Season\SeasonApplier;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

use function sprintf;

#[AsCommand(
    name: 'chameleon-login:apply',
    description: 'Writes the currently active login season into a config file.',
)]
final class ApplySeasonCommand extends Command
{
    public function __construct(
        private readonly SeasonApplier $seasonApplier,
        private readonly LanguageServiceFactory $languageServiceFactory,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $season = $this->seasonApplier->apply();

        $lang = $this->languageServiceFactory->create('default');
        $output->writeln(
            null !== $season
            ? sprintf('<info>'.$lang->sL('typo3_chameleon_login.messages:command.activeSeason').'</info>', $season['title'])
            : '<comment>'.$lang->sL('typo3_chameleon_login.messages:command.noActiveSeason').'</comment>',
        );

        return Command::SUCCESS;
    }
}
