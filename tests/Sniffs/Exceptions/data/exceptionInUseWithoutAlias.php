<?php

declare(strict_types = 1);

namespace Test;

use Exception;
use LogicException;
use RuntimeException;

class exceptionInUseWithoutAlias
{
    public function logicException(): void
    {
        throw new LogicException();
    }

    public function runtimeException(): void
    {
        throw new RuntimeException();
    }

    public function exception(): void
    {
        throw new Exception();
    }
}
