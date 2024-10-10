<?php

declare(strict_types=1);

final class LazyInclusion implements Inclusion
{
    private string $file;
    private Inclusion $instance;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function inline(bool $withImports = false): string
    {
        return $this->getInstance()->inline($withImports);
    }

    public function uninlinableIncludes(): array
    {
        return $this->getInstance()->uninlinableIncludes();
    }

    public function inlined(): bool
    {
        return $this->getInstance()->inlined();
    }

    private function getInstance(): Inclusion
    {
        if (! isset($this->instance)) {
            $this->instance = new InclusionTreeNode($this->file);
        }

        return $this->instance;
    }
}
