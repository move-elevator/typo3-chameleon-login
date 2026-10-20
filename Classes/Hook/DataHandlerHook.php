<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Hook;

use MoveElevator\Typo3ChameleonLogin\Season\SeasonApplier;
use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * Re-applies the active season as soon as season records are saved, hidden, deleted or moved,
 * so editors see the result on the login screen without waiting for the scheduler.
 */
final readonly class DataHandlerHook
{
    public function __construct(
        private SeasonApplier $seasonApplier,
    ) {
    }

    public function processDatamap_afterAllOperations(DataHandler $dataHandler): void
    {
        if (isset($dataHandler->datamap[SeasonApplier::TABLE])) {
            $this->seasonApplier->apply();
        }
    }

    public function processCmdmap_afterFinish(DataHandler $dataHandler): void
    {
        if (isset($dataHandler->cmdmap[SeasonApplier::TABLE])) {
            $this->seasonApplier->apply();
        }
    }
}
