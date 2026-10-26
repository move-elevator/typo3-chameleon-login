<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Upgrades;

use MoveElevator\Typo3ChameleonLogin\Season\SeasonRepository;
use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Upgrades\{DatabaseUpdatedPrerequisite, UpgradeWizardInterface};

#[UpgradeWizard('typo3ChameleonLogin_seasonRootLevelMigration')]
final readonly class SeasonRootLevelMigration implements UpgradeWizardInterface
{
    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    public function getTitle(): string
    {
        return 'Move login seasons to the root level';
    }

    public function getDescription(): string
    {
        return 'Login seasons are managed in the module "Login seasons" and no longer need a sys folder. '
            .'This moves all seasons and their file references to the root level (pid 0).';
    }

    public function updateNecessary(): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(SeasonRepository::TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        return (int) $queryBuilder
            ->count('uid')
            ->from(SeasonRepository::TABLE)
            ->where($queryBuilder->expr()->neq('pid', 0))
            ->executeQuery()
            ->fetchOne() > 0;
    }

    public function executeUpdate(): bool
    {
        $this->moveToRootLevel(SeasonRepository::TABLE);
        $this->moveToRootLevel('sys_file_reference', SeasonRepository::TABLE);

        return true;
    }

    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    private function moveToRootLevel(string $table, ?string $foreignTable = null): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->update($table)
            ->set('pid', 0)
            ->where($queryBuilder->expr()->neq('pid', 0));
        if (null !== $foreignTable) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter($foreignTable)),
            );
        }
        $queryBuilder->executeStatement();
    }
}
