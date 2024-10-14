<?php

declare(strict_types=1);

namespace Inliner\Inclusion\Import;

use Inliner\FileSystem;
use Inliner\Inclusion\Index\Index;

final class Resolver
{
    private Index $index;
    private string $libsDir;

    public function __construct(Index $index, string $libsDir)
    {
        $this->index = $index;
        $this->libsDir = $libsDir;
    }

    /**
     * @return InlinableImport[]
     */
    public function resolve(string $inclusionFile, string ...$importedFiles): array
    {
        return array_map(
            function (string $file): Import {
                $fileAbsoluteName = FileSystem::removeExtension($this->libsDir . DIRECTORY_SEPARATOR . $file);

                if ($this->index->exists($fileAbsoluteName)) {
                    return new InlinableImport($file, $fileAbsoluteName);
                }

                return new BuiltinImport($file);
            },
            array_filter(
                $importedFiles,
                fn (string $file): bool => ! $this->isSelfReferencingLibImport($inclusionFile, $file)
            )
        );
    }

    private function isSelfReferencingLibImport(string $inclusionFile, string $fileImport): bool
    {
        return strpos($inclusionFile, $this->libsDir) === 0
            && FileSystem::filename($fileImport) === FileSystem::filename($inclusionFile);
    }

}
