<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

return new Configuration()
    ->addPathToScan(__DIR__ . '/ext_localconf.php', isDev: false);
