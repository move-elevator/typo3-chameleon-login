<?php

declare(strict_types=1);

use Composer\Autoload;
use ShipMonk\ComposerDependencyAnalyser;

$rootPath = \dirname(__DIR__, 2);

/** @var Autoload\ClassLoader $loader */
$loader = require $rootPath.'/vendor/autoload.php';
$loader->register();

$configuration = new ComposerDependencyAnalyser\Config\Configuration();
$configuration
    ->addPathToScan($rootPath.'/Configuration', false)
    ->addPathsToExclude([
        $rootPath.'/Tests/CGL',
    ])
;

return $configuration;
