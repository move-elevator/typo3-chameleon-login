<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php85\Rector\Property\AddOverrideAttributeToOverriddenPropertiesRector;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\CodeQuality\General\{ConvertImplicitVariablesToExplicitGlobalsRector,
    GeneralUtilityMakeInstanceToConstructorPropertyRector};
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\Set\{Typo3LevelSetList, Typo3SetList};

$rootPath = dirname(__DIR__, 2);

return RectorConfig::configure()
    ->withPaths([
        $rootPath.'/Classes',
        $rootPath.'/Configuration',
    ])
    ->withPhpVersion(PhpVersion::PHP_85)
    ->withSets([
        Typo3SetList::CODE_QUALITY,
        Typo3SetList::GENERAL,
        Typo3LevelSetList::UP_TO_TYPO3_14,
        LevelSetList::UP_TO_PHP_85,
    ])
    // To have a better analysis from PHPStan, we teach it here some more things
    ->withPHPStanConfigs([
        Typo3Option::PHPSTAN_FOR_RECTOR_PATH,
    ])
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
        ConvertImplicitVariablesToExplicitGlobalsRector::class,
    ])
    // If you use withImportNames(), you should consider excluding some
    // TYPO3 files.
    ->withSkip([
        NameImportingPostRector::class => [
            'ClassAliasMap.php',
        ],
        GeneralUtilityMakeInstanceToConstructorPropertyRector::class,
        // `#[\Override]` may only target properties as of PHP 8.5, while this
        // package still supports 8.4 — there the attribute is a fatal error.
        // Drop this entry once the PHP requirement moves past 8.4.
        AddOverrideAttributeToOverriddenPropertiesRector::class,
    ])
;
