<?php

declare(strict_types=1);

include 'FileSystem.php';
include 'Inclusion.php';
include 'InclusionRepository.php';
include 'InclusionTreeNode.php';
include 'LazyInclusion.php';

define("TOOLBOX_DIR", dirname(__DIR__) . '/src/Toolbox');
define("PROBLEMS_DIR", dirname(__DIR__) . '/src/Problems');

InclusionRepository::addAll(
    ...array_map(
           static fn (string $file): string => FileSystem::removeExtension($file),
           [
               ...FileSystem::filesRecursive(
                   TOOLBOX_DIR,
                   static fn (string $file): bool => pathinfo($file, PATHINFO_EXTENSION) === 'c'
               ),
               ...FileSystem::filesRecursive(
                   PROBLEMS_DIR,
                   static fn (string $file): bool => pathinfo($file, PATHINFO_BASENAME) === 'main.c'
               ),

           ]
       )
);

var_export(
    InclusionRepository::get('/Users/vladimir.cheskidov/Projects/Spoj/SpojС/src/Problems/HalfOfTheHalf/main')->inline(true)
);