<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$sourceDirectories = array_filter(
    [
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/config',
        __DIR__ . '/database/migrations',
    ],
    static fn(string $directory): bool => is_dir($directory),
);

$finder = Finder::create()
    ->in($sourceDirectories)
    ->append([
        __FILE__,
        __DIR__ . '/cli-config.php',
    ]);

return new Config()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PHP8x5Migration' => true,
        '@PHPUnit10x0Migration:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'trailing_comma_in_multiline' => true,
    ])
    ->setFinder($finder);
