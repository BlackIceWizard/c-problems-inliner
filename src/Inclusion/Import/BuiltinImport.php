<?php

declare(strict_types=1);

namespace Inliner\Inclusion\Import;

final class BuiltinImport implements Import
{
    private string $argument;

    public function __construct(string $argument)
    {
        $this->argument = $argument;
    }

    public function file(): string
    {
        return $this->argument;
    }
}
