<?php

declare(strict_types=1);

namespace Test;

use Foo\LogicException;
use Bar\BazException;

class AllowedExceptions
{
    public function a()
    {
        throw new LogicException();
    }

    public function b()
    {
        throw new BazException();
    }
}