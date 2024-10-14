<?php

declare(strict_types=1);

namespace Inliner\Inclusion\Index;

use Closure;
use Inliner\Inclusion\Role;

final class Selector
{
    private string $sourceDir;
    private Closure $filterCallable;
    private Role $role;

    private function __construct(string $sourceDir, Closure $filter, Role $role)
    {
        $this->sourceDir = $sourceDir;
        $this->filterCallable = $filter;
        $this->role = $role;
    }

    public static function ofLibs(string $sourceDir, Closure $filter): self
    {
        return new self($sourceDir, $filter, Role::LIB());
    }

    public static function ofExecutables(string $sourceDir, Closure $filter): self
    {
        return new self($sourceDir, $filter, Role::EXECUTABLE());
    }

    public function sourceDir(): string
    {
        return $this->sourceDir;
    }

    public function filter(): Closure
    {
        return $this->filterCallable;
    }

    public function isLibs(): bool
    {
        return $this->role->isLib();
    }

    public function isExecutables(): bool
    {
        return $this->role->isExecutable();
    }
}
