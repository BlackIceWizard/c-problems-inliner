<?php

declare(strict_types=1);

namespace Inliner\Inclusion\Index;

use Inliner\FileSystem;

final class Factory
{
    public static function createIndex(Selector ...$selectors): Index
    {
        return new Index(
            self::collectInclusionFiles(
                ...array_filter(
                    $selectors,
                    static fn (Selector $selector): bool => $selector->isLibs()
                )
            ),
            self::collectInclusionFiles(
                ...array_filter(
                       $selectors,
                       static fn (Selector $selector): bool => $selector->isExecutables()
                   )
            )
        );
    }

    /**
     * @return string[]
     */
    private static function collectInclusionFiles(Selector ...$selectors): array
    {
        return array_map(
            static fn (string $file): string => FileSystem::removeExtension($file),
            array_merge(
                ...array_map(
                       static fn (Selector $selector): array => FileSystem::filesRecursive($selector->sourceDir(), $selector->filter()),
                       $selectors
                   )
            )
        );
    }
}
