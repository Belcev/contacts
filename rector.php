<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\Level\TypeDeclarationLevel;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withParallel()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/bootstrap/cache',
    ])
    ->withCache(
        cacheDirectory: __DIR__ . '/tmp/rector',
        cacheClass: FileCacheStorage::class,
    )
    ->withPhpSets()
    ->withSets(
        [
            SetList::CARBON,
            SetList::EARLY_RETURN,
            SetList::INSTANCEOF,
            SetList::PRIVATIZATION,
            SetList::RECTOR_PRESET,
            SetList::STRICT_BOOLEANS,
        ]
    )
    ->withTypeCoverageLevel(count(TypeDeclarationLevel::RULES))
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
    )
    ->withImportNames(removeUnusedImports: true)
    ->withSetProviders(LaravelSetProvider::class);
