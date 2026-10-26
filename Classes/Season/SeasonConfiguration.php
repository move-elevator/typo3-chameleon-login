<?php

declare(strict_types=1);

namespace MoveElevator\Typo3ChameleonLogin\Season;

use DateTimeImmutable;
use MoveElevator\Typo3ChameleonLogin\Style\LoginStyles;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileRepository;

use function array_filter;
use function count;
use function preg_match;
use function trim;

final readonly class SeasonConfiguration
{
    public function __construct(
        private SeasonRepository $seasonRepository,
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
            $images = $this->resolveImages((int) $season['uid'], 'image');
            if ([] !== $images) {
                $backend['loginBackgroundImage'] = $images[0]['url'];
                $extension['loginBackgroundImages'] = $images;
            }
            $darkImages = $this->resolveImages((int) $season['uid'], 'image_dark');
            if ([] !== $darkImages) {
                $extension['loginBackgroundImagesDark'] = $darkImages;
            }
        }
        $backgroundColor = (string) ($season['background_color'] ?? '');
        if ('' !== $backgroundColor) {
            $extension['loginBackgroundColor'] = $backgroundColor;
        }
        $extension += $this->resolveDarkColors($season);
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
        $today = new DateTimeImmutable('today');
        foreach ($this->seasonRepository->findEnabled() as $row) {
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
        $colors = $this->resolveGradientColors($season, '');
        if (count($colors) < 2) {
            return [];
        }

        $gradient = [
            'loginBackgroundGradient' => $colors,
            'loginBackgroundGradientSpeed' => (string) ($season['gradient_speed'] ?? ''),
        ];
        $darkColors = $this->resolveGradientColors($season, '_dark');
        if (count($darkColors) > 1) {
            $gradient['loginBackgroundGradientDark'] = $darkColors;
        }

        return $gradient;
    }

    /**
     * @param array<string, mixed> $season
     *
     * @return list<string>
     */
    private function resolveGradientColors(array $season, string $suffix): array
    {
        return LoginStyles::filterValidColors([
            $season['gradient_color_1'.$suffix] ?? '',
            $season['gradient_color_2'.$suffix] ?? '',
            $season['gradient_color_3'.$suffix] ?? '',
            $season['gradient_color_4'.$suffix] ?? '',
        ]);
    }

    /**
     * @param array<string, mixed> $season
     *
     * @return array<string, string>
     */
    private function resolveDarkColors(array $season): array
    {
        return array_filter(
            [
                'loginBackgroundColorDark' => (string) ($season['background_color_dark'] ?? ''),
                'loginHighlightColorDark' => (string) ($season['highlight_color_dark'] ?? ''),
            ],
            LoginStyles::isValidColor(...),
        );
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
        $logo = $this->resolveImages((int) $season['uid'], 'logo')[0]['url'] ?? null;
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
     * @return list<array{url: string, position: string}>
     */
    private function resolveImages(int $uid, string $field): array
    {
        $images = [];
        foreach ($this->fileRepository->findByRelation(SeasonRepository::TABLE, $field, $uid) as $reference) {
            $properties = $reference->getReferenceProperties();
            // Outside the frontend, findByRelation() also returns hidden references
            if ((bool) ($properties['hidden'] ?? false)) {
                continue;
            }
            $url = (string) $reference->getOriginalFile()->getPublicUrl();
            // Defense in depth: skip paths containing characters that could
            // break out of a CSS `url('…')` declaration when consumed downstream.
            if ('' !== $url && 1 !== preg_match('/[\'"\\\\(){};\r\n\t]/', $url)) {
                $focusArea = CropVariantCollection::create((string) ($properties['crop'] ?? ''))->getFocusArea();
                $images[] = ['url' => $url, 'position' => LoginStyles::backgroundPosition($focusArea)];
            }
        }

        return $images;
    }
}
