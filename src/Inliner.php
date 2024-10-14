<?php

declare(strict_types=1);

namespace Inliner;

final class Inliner
{
    private Inclusion\Factory $factory;
    private Inclusion\Renderer\Renderer $renderer;

    public function __construct(Inclusion\Index\Index $index, string $libsDir)
    {
        $this->factory = new Inclusion\Factory(
            $index,
            new Inclusion\Repository(),
            new Inclusion\Import\Resolver($index, $libsDir)
        );
        $this->renderer = new Inclusion\Renderer\Renderer();
    }

    public function inline(string $file): string
    {
        return $this->renderer->render($this->factory->create($file));
    }
}
