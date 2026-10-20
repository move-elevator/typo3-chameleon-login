<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__, 2);

$finder = new PhpCsFixer\Finder()
    ->exclude('node_modules')
    ->ignoreVCSIgnored(true)
    ->in(realpath($rootPath));

return new PhpCsFixer\Config()
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'group_import' => true,
        'native_function_invocation' => ['include' => ['@all'], 'scope' => 'namespaced', 'strict' => true],
        'no_unused_imports' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'single_import_per_statement' => false,
        'trailing_comma_in_multiline' => true,
        'yoda_style' => true,
    ])
    ->setFinder($finder);
