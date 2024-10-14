<?php

declare(strict_types=1);

namespace Inliner\Inclusion\Renderer;

use Inliner\Inclusion\Inclusion;

final class Renderer
{
    public function render(Inclusion $inclusion): string
    {
        $context = new Context();

        return $this->renderRecursively($inclusion, $context, true);
    }

    private function renderRecursively(Inclusion $inclusion, Context $context, bool $firstLevel = false): string
    {
        $context->addRendered($inclusion->file());

        $result = '';

        if ($firstLevel) {
            $result .= $this->renderBuiltinIncludes($inclusion);
            $result .= $this->renderDefines($inclusion);
        }

        $result .= implode(
            PHP_EOL,
            [
                ...array_map(
                    fn (Inclusion $dependency): string => $this->renderRecursively($dependency, $context),
                    array_filter(
                        $inclusion->dependencies(),
                        static fn (Inclusion $dependency): bool => ! $context->rendered($dependency->file())
                    )
                ),
                $this->removeDefines(
                    $this->removeIncludes($inclusion->content())
                ),
            ]
        );

        return $result . PHP_EOL;
    }

    private function renderBuiltinIncludes(Inclusion $inclusion): string
    {
        $builtinIncludes = array_map(
            static fn (string $file): string => sprintf('#include "%s"', $file),
            $inclusion->builtinImportFilesRecursively()
        );

        return (count($builtinIncludes) > 0 ? implode(PHP_EOL, $builtinIncludes) . PHP_EOL . PHP_EOL : '');
    }

    private function renderDefines(Inclusion $inclusion): string
    {
        $defines = array_map(
            static fn(string $definition): string => sprintf('#define %s', $definition),
            $inclusion->definesRecursively()
        );

        return (count($defines) > 0 ? implode(PHP_EOL, $defines) . PHP_EOL. PHP_EOL : '');
    }

    private function removeIncludes(string $content): string
    {
        return trim(preg_replace('/#include [<"].+[>"]/i', '', $content));
    }

    private function removeDefines(string $content): string
    {
        return trim(preg_replace('/#define .+/i', '', $content));
    }
}
