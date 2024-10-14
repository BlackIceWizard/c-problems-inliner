<?php

declare(strict_types=1);

namespace Inliner\Inclusion;

use InvalidArgumentException;

class Repository
{
    /** @var array<string, Inclusion> */
    private array $inclusions = [];

    public function add(Inclusion ...$inclusions): void
    {
        foreach ($inclusions as $inclusion) {
            $this->inclusions[$inclusion->file()] = $inclusion;
        }
    }

    public function get(string $file): Inclusion
    {
        if (! isset($this->inclusions[$file])) {
            throw new InvalidArgumentException("Inclusion not found");
        }

        return $this->inclusions[$file];
    }

    public function exists(string $file): bool
    {
        return isset($this->inclusions[$file]);
    }
}