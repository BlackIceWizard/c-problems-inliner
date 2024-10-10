<?php

declare(strict_types=1);

class FileSystem
{
    /**
     * @return string[]
     */
    private static function elements(string $dir, bool $isDir): array
    {
        $files = scandir($dir);
        $files = array_values(array_diff($files, ['.', '..']));

        $files = array_map(
            static fn ($file): string => $dir . '/' . $file,
            $files
        );

        return array_filter(
            $files,
            static fn ($file): bool => $isDir ? is_dir($file) : is_file($file)
        );
    }

    public static function files(string $dir): array
    {
        return self::elements($dir, false);
    }

    public static function filesRecursive(string $dir, callable $filter): array
    {
        $files = [self::files($dir)];
        $dirs = self::directories($dir);

        foreach ($dirs as $subDir) {
            $files[] = self::filesRecursive($subDir, $filter);
        }

        return array_filter(
            array_merge(...$files),
            $filter
        );
    }

    public static function directories(string $dir): array
    {
        return self::elements($dir, true);
    }

    public static function removeExtension(string $file): string
    {
        return substr($file, 0, -2);
    }
}