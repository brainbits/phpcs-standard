<?php

declare(strict_types=1);

namespace Test;

use Exception;
use LogicException;
use RuntimeException;

class GlobalExceptionSniff
{
    public function test(): void
    {
        throw new Exception('foo');
        throw new RuntimeException('foo');
        throw new LogicException('foo');
    }
}
