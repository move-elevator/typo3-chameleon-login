<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Season;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;

final readonly class SeasonRepository
{
    public const string TABLE = 'tx_typo3chameleonlogin_domain_model_season';

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function findEnabled(): array
    {
        return $this->fetchOrdered($this->connectionPool->getQueryBuilderForTable(self::TABLE));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findAll(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        return $this->fetchOrdered($queryBuilder);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchOrdered(QueryBuilder $queryBuilder): array
    {
        return $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->orderBy('priority', 'DESC')
            ->addOrderBy('uid', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
