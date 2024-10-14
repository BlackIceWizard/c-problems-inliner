<?php

declare(strict_types=1);

namespace Inliner\Inclusion;

use Inliner\FileSystem;

final class Factory
{
    private Repository $inclusions;
    private Index\Index $index;
    private Import\Resolver $importResolver;

    public function __construct(Index\Index $index, Repository $inclusions, Import\Resolver $importResolver)
    {
        $this->inclusions = $inclusions;
        $this->index = $index;
        $this->importResolver = $importResolver;
    }

    public function create(string $file): Inclusion
    {
        if ($this->inclusions->exists($file)) {
            return $this->inclusions->get($file);
        }

        $content = FileSystem::readFile($file . '.c');
        $imports = $this->importResolver->resolve($file, ...$this->parseImportedFiles($content));
        $defines = $this->parseDefines($content);

        if (FileSystem::fileExists($file . '.h')) {
            $defines = array_merge(
                $defines,
                $this->parseDefines(FileSystem::readFile($file . '.h'))
            );
        }

        $inclusion = new Inclusion(
            $this->index->role($file),
            $file,
            dirname($file),
            $content,
            $imports,
            $defines,
            $this->createDependencies(...$imports)
        );

        $this->inclusions->add($inclusion);

        return $inclusion;
    }

    /**
     * @return Inclusion[]
     */
    private function createDependencies(Import\Import ...$imports): array
    {
        return array_map(
            fn (Import\InlinableImport $import): Inclusion => $this->create($import->fileAbsoluteName()),
            array_filter(
                $imports,
                static fn (Import\Import $import): bool => $import instanceof Import\InlinableImport
            )
        );
    }

    /**
     * @return string[]
     */
    private function parseImportedFiles(string $content): array
    {
        $matches = [];
        preg_match_all('/#include (<(?<headers>.+)>|"(?<files>.+)")/i', $content, $matches);

        return array_filter(
            array_merge($matches['headers'], $matches['files']),
            static fn (string $include): bool => $include !== ''
        );
    }

    /**
     * @return string[]
     */
    private function parseDefines(string $content): array
    {
        $matches = [];
        preg_match_all('/#define +(?<definitions>[A-Z_]+ +.+)/i', $content, $matches);

        return $matches['definitions'];
    }
}
