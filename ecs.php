<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withRules([
        NoUnusedImportsFixer::class,
    ])
    ->withRootFiles()
    ->withCache(
        directory: __DIR__ . '/tmp/ecs',
    )
    ->withSkip([
        __DIR__ . '/bootstrap/cache',
        ReferenceUsedNamesOnlySniff::class => [
            __DIR__ . '/ecs.php',
            __DIR__ . '/rector.php',
        ],
        StaticClosureSniff::class => [
            __DIR__ . '/database/factories',
        ],
    ])
    ->withPhpCsFixerSets(psr12: true)
    ->withSpacing(indentation: Option::INDENTATION_SPACES, lineEnding: PHP_EOL)

;
