<?php

declare(strict_types=1);

class InclusionTreeNode implements Inclusion
{
    private string $file;

    private bool $inlined = false;

    /** @var Inclusion[] */
    private array $dependencies;
    /** @var string[] */
    private array $canonizedIncludes;
    private string $content;
    private bool $isMain;
    private string $directory;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->directory = dirname($this->file);
        $this->isMain = strpos($this->file, PROBLEMS_DIR) === 0;
        $this->content = file_get_contents($this->file . '.c');
        $this->canonizedIncludes = $this->canonizeIncludes(...$this->includes());
        $this->dependencies = $this->parseDependencies();
    }

    public function inline(bool $withImports = false): string
    {
        $this->inlined = true;

        $result = '';

        if ($withImports) {
            $uninlinableIncludes = array_map(
                static fn (string $include): string => "#include <$include>",
                $this->uninlinableIncludes()
            );

            $result .= (count($uninlinableIncludes) > 0 ? implode(PHP_EOL, $uninlinableIncludes) . PHP_EOL : '');
        }

        $result .= implode(
            PHP_EOL,
            [
                ...array_filter(
                    array_map(
                        static fn (Inclusion $dependency): string => $dependency->inlined() ? '' : $dependency->inline(),
                        $this->dependencies
                    ),
                    static fn (string $inlined): bool => $inlined !== ''
                ),
                trim(preg_replace('/#include [<"].+[>"]/i', '', $this->content)),
            ]
        );

        return $result;
    }

    public function inlined(): bool
    {
        return $this->inlined;
    }

    /**
     * @return Inclusion[]
     */
    private function parseDependencies(): array
    {
        return InclusionRepository::intersection(
            ...array_map(
                   fn (string $include): string => $this->canonizedIncludeToFileNameWithoutExtension($include),
                   array_filter(
                       $this->canonizedIncludes,
                       fn (string $include): bool => $this->isInlinableInclude($include)
                           && $this->canonizedIncludeToFileNameWithoutExtension($include) !== $this->file
                   )
               )
        );
    }

    /**
     * @return string[]
     */
    public function uninlinableIncludes(): array
    {
        return array_unique(
            [
                ...array_filter(
                    $this->canonizedIncludes,
                    fn (string $include): bool => ! $this->isInlinableInclude($include)
                ),
                ...array_merge(
                    ...array_map(
                           static fn (Inclusion $dependency): array => $dependency->uninlinableIncludes(),
                           $this->dependencies
                       )
                ),
            ]
        );
    }

    private function isInlinableInclude(string $include): bool
    {
        return InclusionRepository::exists($this->canonizedIncludeToFileNameWithoutExtension($include));
    }

    /**
     * @return string[]
     */
    private function includes(): array
    {
        $matches = [];
        preg_match_all('/#include (<(?<dependency>.+)>|"(?<dependency2>.+)")/i', $this->content, $matches);

        return array_filter(
            array_merge($matches['dependency'], $matches['dependency2']),
            static fn (string $include): bool => $include !== ''
        );
    }

    /**
     * @return string[]
     */
    private function canonizeIncludes(string ...$includes): array
    {
        if ($this->isMain) {
            return $includes;
        }

        return array_map(
            function (string $include): string {
                if ($this->directory === TOOLBOX_DIR) {
                    return $include;
                }

                if (InclusionRepository::exists(FileSystem::removeExtension($this->directory . '/' . $include))) {
                    return str_replace(TOOLBOX_DIR . '/', '', $this->directory) . '/' . $include;
                }

                return $include;
            },
            $includes
        );
    }

    private function canonizedIncludeToFileNameWithoutExtension(string $include): string
    {
        return FileSystem::removeExtension(TOOLBOX_DIR . '/' . $include);
    }
}