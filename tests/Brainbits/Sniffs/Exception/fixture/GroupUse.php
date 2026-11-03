<?php

declare(strict_types=1);

use Foo\{Exception};
use Foo\{Bar as RuntimeException, function baz};
use function Foo\LogicException;
use const Foo\DomainException;
use Baz\{
    /** doc comment */
    InvalidArgumentException,
};
use Baz\LengthException /** doc comment */;

throw new Exception();
throw new RuntimeException();
throw new LogicException();
throw new DomainException();
throw new InvalidArgumentException();
throw new LengthException();
