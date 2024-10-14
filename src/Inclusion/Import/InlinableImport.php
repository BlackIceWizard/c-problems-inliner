<?php

declare(strict_types=1);

namespace Inliner\Inclusion\Import;

final class InlinableImport implements Import
{
    private string $file;
    private string $fileAbsoluteName;

    public function __construct(string $file, string $fileAbsoluteName)
    {
        $this->file = $file;
        $this->fileAbsoluteName = $fileAbsoluteName;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function fileAbsoluteName(): string
    {
        return $this->fileAbsoluteName;
    }
}
