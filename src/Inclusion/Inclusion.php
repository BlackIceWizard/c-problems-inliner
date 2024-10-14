<?php

declare(strict_types=1);

namespace Inliner\Inclusion;

use Inliner\Inclusion\Import\BuiltinImport;
use Inliner\Inclusion\Import\Import;

class Inclusion
{
    private string $file;
    private array $dependencies;
    private array $imports;
    private string $content;
    private Role $role;
    private array $defines;
    private string $directory;

    /**
     * @param Inclusion[] $dependencies
     * @param Import[] $imports
     * @param string[] $defines
     */
    public function __construct(
        Role $role,
        string $file,
        string $directory,
        string $content,
        array $imports,
        array $defines,
        array $dependencies
    ) {
        $this->role = $role;
        $this->file = $file;
        $this->directory = $directory;
        $this->content = $content;
        $this->imports = $imports;
        $this->defines = $defines;
        $this->dependencies = $dependencies;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function content(): string
    {
        return $this->content;
    }

    /**
     * @return string[]
     */
    public function builtinImportFilesRecursively(): array
    {
        return array_unique(
            array_merge(
                   array_map(
                       static fn (BuiltinImport $import): string => $import->file(),
                       array_filter(
                           $this->imports,
                           static fn (Import $import): bool => $import instanceof BuiltinImport
                       )
                   ),
                ...array_map(
                       static fn (Inclusion $dependency): array => $dependency->builtinImportFilesRecursively(),
                       $this->dependencies
                   )

            )
        );
    }

    /**
     * @return string[]
     */
    public function definesRecursively(): array
    {
        return array_unique(
            array_merge(
                   $this->defines,
                ...array_map(
                       static fn (Inclusion $dependency): array => $dependency->definesRecursively(),
                       $this->dependencies
                   )
            )
        );
    }

    public function dependencies(): array
    {
        return $this->dependencies;
    }
}