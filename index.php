<?php

declare(strict_types=1);

use Inliner\FileSystem;
use Inliner\Inclusion\Index;
use Inliner\Inliner;

require_once __DIR__ . '/vendor/autoload.php';

$libsDir = dirname(__DIR__) . '/src/Toolbox';
$executablesDir = dirname(__DIR__) . '/src/Problems';

$index = Index\Factory::createIndex(
    Index\Selector::ofLibs(
        $libsDir,
        static fn (string $file): bool => pathinfo($file, PATHINFO_EXTENSION) === 'c'
    ),
    Index\Selector::ofExecutables(
        $executablesDir,
        static fn (string $file): bool => pathinfo($file, PATHINFO_BASENAME) === 'main.c'
    )
);

$inliner = new Inliner($index, $libsDir);

$i = 0;
foreach ($index->executables() as $executable) {
    $i++;
    $composedAbsoluteName = $executable . '_assembled.c';
    FileSystem::writeFile($composedAbsoluteName, $inliner->inline($executable));
    echo sprintf('%d) Assembled: %s', $i, $composedAbsoluteName) . PHP_EOL;
}
