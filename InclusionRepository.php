<?php

declare(strict_types=1);

class InclusionRepository
{
    /** @var Inclusion[] */
    private static array $inclusions = [];

    public static function addAll(string ...$files): void
    {
        foreach ($files as $file) {
            self::$inclusions[$file] = new LazyInclusion($file);
        }
    }

    public static function get(string $file): Inclusion
    {
        if (! isset(self::$inclusions[$file])) {
            throw new InvalidArgumentException("Inclusion $file not found");
        }

        return self::$inclusions[$file];
    }

    /**
     * @return Inclusion[]
     */
    public static function intersection(string ...$flies): array
    {
        $result = [];
        foreach ($flies as $file) {
            if (isset(self::$inclusions[$file])) {
                $result[] = self::$inclusions[$file];
            }
        }

        return $result;
    }

    public static function exists(string $file): bool
    {
        return isset(self::$inclusions[$file]);
    }
}