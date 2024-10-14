<?php

declare(strict_types=1);

namespace Inliner;

class FileSystem
{
    private const LOOK_FOR_DIRS = 1;
    private const LOOK_FOR_FILES = 2;

    public static function files(string $dir): array
    {
        return self::elements($dir, self::LOOK_FOR_FILES);
    }

    /**
     * @return string[]
     */
    public static function filesRecursive(string $dir, callable $filter): array
    {
        $files = [self::files($dir)];

        foreach (self::directories($dir) as $subDir) {
            $files[] = self::filesRecursive($subDir, $filter);
        }

        return array_filter(array_merge(...$files), $filter);
    }

    public static function directories(string $dir): array
    {
        return self::elements($dir, self::LOOK_FOR_DIRS);
    }

    /**
     * @return string[]
     */
    private static function elements(string $dir, int $lookFor): array
    {
        return array_filter(
            array_map(
                static fn ($file): string => $dir . DIRECTORY_SEPARATOR . $file,
                array_values(array_diff(scandir($dir), ['.', '..']))
            ),
            $lookFor === self::LOOK_FOR_DIRS
                ? static fn ($file): bool => is_dir($file)
                : static fn ($file): bool => is_file($file)
        );
    }

    public static function removeExtension(string $file): string
    {
        return pathinfo($file, PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.pathinfo($file, PATHINFO_FILENAME);
    }

    public static function filename(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    public static function readFile(string $file): string
    {
        return file_get_contents($file);
    }

    public static function fileExists(string $file): bool
    {
        return file_exists($file);
    }

    public static function writeFile($executable, $content): void
    {
        file_put_contents($executable, $content);
    }
}