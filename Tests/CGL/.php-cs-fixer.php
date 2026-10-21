<?php

declare(strict_types=1);

use KonradMichalik\PhpCsFixerPreset\Config;
use KonradMichalik\PhpCsFixerPreset\Rules\Set\Set;
use Symfony\Component\Finder\Finder;

$rootPath = \dirname(__DIR__, 2);

return Config::create()
    ->withRule(
        Set::fromArray([
            'global_namespace_import' => [
                'import_constants' => true,
            ],
            'native_function_invocation' => [
                'include' => ['@all'],
            ],
        ]),
    )
    ->withFinder(
        static fn (Finder $finder) => $finder->in($rootPath),
    );
