<?php

declare(strict_types=1);

namespace Inliner\Inclusion\Renderer;

final class Context
{
    /** @var array<string, bool>  */
    private array $rendered = [];

    public function addRendered(string $file): void
    {
        $this->rendered[$file] = true;
    }

    public function rendered(string $file): bool
    {
        return isset($this->rendered[$file]);
    }
}
