<?php

declare(strict_types=1);

namespace Inliner\Inclusion\Index;
use Inliner\Inclusion\Role;
use InvalidArgumentException;

final class Index
{
    /** @var array<string, int> */
    private array $libs;

    /** @var array<string, int> */
    private array $executables;

    /**
     * @param string[] $libs
     * @param string[] $executables
     */
    public function __construct(array $libs, array $executables)
    {
        $this->libs = array_flip(array_values($libs));
        $this->executables = array_flip(array_values($executables));
    }

    public function exists(string $file): bool
    {
        return isset($this->libs[$file]) || isset($this->executables[$file]);
    }

    public function role(string $file): Role
    {
        if (isset($this->libs[$file])) {
            return Role::LIB();
        }

        if (isset($this->executables[$file])) {
            return Role::EXECUTABLE();
        }

        throw new InvalidArgumentException("File $file is not in the index");
    }

    public function isExecutable(string $file): bool
    {
        return isset($this->executables[$file]);
    }

    public function isLib(string $file): bool
    {
        return isset($this->libs[$file]);
    }

    public function executables(): array
    {
        return array_keys($this->executables);
    }
}
