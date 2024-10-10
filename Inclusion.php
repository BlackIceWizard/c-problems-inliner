<?php

declare(strict_types=1);

interface Inclusion
{
    public function inline(bool $withImports = false): string;

    /**
     * @return string[]
     */
    public function uninlinableIncludes(): array;

    public function inlined(): bool;
}
