<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Season;

use DateTimeImmutable;
use MoveElevator\Typo3ChameleonLogin\Style\LoginStyles;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileRepository;

use function array_filter;
use function count;
use function preg_match;
use function trim;

final readonly class SeasonConfiguration
{
    private const string TABLE = 'tx_typo3chameleonlogin_domain_model_season';

    public function __construct(
        private ConnectionPool $connectionPool,
        private FileRepository $fileRepository,
        private SeasonMatcher $seasonMatcher,
    ) {}

    /**
     * Returns the TYPO3_CONF_VARS overrides of the currently active season.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $season = $this->findActiveSeason();

        return null === $season ? [] : $this->buildConfig($season);
    }

    /**
     * @param array<string, mixed> $season
     *
     * @return array<string, mixed>
     */
    private function buildConfig(array $season): array
    {
        $backend = $this->resolveBranding($season);
        $extension = [];

        if (($season['background_type'] ?? '') === 'gradient') {
            $extension = $this->resolveGradient($season);
        } else {
            $images = $this->resolveImagePaths((int) $season['uid'], 'image');
            if ([] !== $images) {
                $backend['loginBackgroundImage'] = $images[0];
            }
            if (count($images) > 1) {
                $extension['loginBackgroundImages'] = $images;
            }
        }
        $backgroundColor = (string) ($season['background_color'] ?? '');
        if ('' !== $backgroundColor) {
            $extension['loginBackgroundColor'] = $backgroundColor;
        }
        $effect = (string) ($season['effect'] ?? '');
        if ('' !== $effect) {
            $extension['effect'] = $effect;
        }

        $extensions = array_filter(['backend' => $backend, 'typo3_chameleon_login' => $extension]);

        return [] === $extensions ? [] : ['EXTENSIONS' => $extensions];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findActiveSeason(): ?array
    {
        $rows = $this->connectionPool
            ->getQueryBuilderForTable(self::TABLE)
            ->select('*')
            ->from(self::TABLE)
            ->orderBy('priority', 'DESC')
            ->addOrderBy('uid', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $today = new DateTimeImmutable('today');
        foreach ($rows as $row) {
            if ($this->seasonMatcher->isActive($row, $today)) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $season
     *
     * @return array<string, mixed>
     */
    private function resolveGradient(array $season): array
    {
        $colors = LoginStyles::filterValidColors([
            $season['gradient_color_1'] ?? '',
            $season['gradient_color_2'] ?? '',
            $season['gradient_color_3'] ?? '',
            $season['gradient_color_4'] ?? '',
        ]);
        if (count($colors) < 2) {
            return [];
        }

        return [
            'loginBackgroundGradient' => $colors,
            'loginBackgroundGradientSpeed' => (string) ($season['gradient_speed'] ?? ''),
        ];
    }

    /**
     * Maps the branding fields of a season to the core backend login options.
     *
     * @param array<string, mixed> $season
     *
     * @return array<string, string>
     */
    private function resolveBranding(array $season): array
    {
        $branding = [];
        $logo = $this->resolveImagePaths((int) $season['uid'], 'logo')[0] ?? null;
        if (null !== $logo) {
            $branding['loginLogo'] = $logo;
            $branding['loginLogoAlt'] = trim((string) ($season['logo_alt'] ?? ''));
        }
        $highlightColor = (string) ($season['highlight_color'] ?? '');
        if (LoginStyles::isValidColor($highlightColor)) {
            $branding['loginHighlightColor'] = $highlightColor;
        }
        $footnote = trim((string) ($season['footnote'] ?? ''));
        if ('' !== $footnote) {
            $branding['loginFootnote'] = $footnote;
        }

        return $branding;
    }

    /**
     * @return list<string>
     */
    private function resolveImagePaths(int $uid, string $field): array
    {
        $urls = [];
        foreach ($this->fileRepository->findByRelation(self::TABLE, $field, $uid) as $reference) {
            $url = (string) $reference->getOriginalFile()->getPublicUrl();
            // Defense in depth: skip paths containing characters that could
            // break out of a CSS `url('…')` declaration when consumed downstream.
            if ('' !== $url && 1 !== preg_match('/[\'"\\\\(){};\r\n\t]/', $url)) {
                $urls[] = $url;
            }
        }

        return $urls;
    }
}
