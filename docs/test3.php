<?php

declare(strict_types = 1);

namespace Brainbits;

use RuntimeException;

/**
 * Class Test3
 *
 * @covers \Brainbits\test3
 */
class test3
{
    public function foo(): void
    {
        throw new \Exception();
        throw new RuntimeException();
        throw new \My\RuntimeException();
    }
}
