<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\{Configuration, NamedFilter};

return static function (Configuration $config): Configuration {
    // Provides the login form that consumes the generated loginBackgroundImage setting
    $config->addNamedFilter(NamedFilter::fromString('typo3/cms-backend'));

    return $config;
};
