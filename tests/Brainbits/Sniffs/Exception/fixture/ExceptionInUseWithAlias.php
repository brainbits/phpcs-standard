<?php

declare(strict_types=1);

namespace Test;

use Exception as Foo;
use RuntimeException as Bar;

class ExceptionInUseWithAlias
{
    public function foo(): void
    {
        throw new Foo();
    }

    public function bar(): void
    {
        throw new Bar();
    }
}
