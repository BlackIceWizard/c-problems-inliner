<?php

declare(strict_types=1);

namespace Inliner\Inclusion;

use MyCLabs\Enum\Enum;

/**
 * @method static Role LIB()
 * @method static Role EXECUTABLE()
 */
final class Role extends Enum
{
    private const LIB = 'lib';
    private const EXECUTABLE = 'executable';

    public function isLib(): bool
    {
        return $this->equals(self::LIB());
    }

    public function isExecutable(): bool
    {
        return $this->equals(self::EXECUTABLE());
    }
}
