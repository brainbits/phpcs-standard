<?php

namespace Test;

use Foo\LogicException;
use Bar\BazException;

class allowedExceptions
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