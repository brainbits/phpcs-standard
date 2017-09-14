<?php

declare(strict_types = 1);

namespace Test;

class fullQualifiedException
{
    public function exception(): void
    {
        throw new \Exception();
    }

    public function invalidArgumentException(): void
    {
        throw new \InvalidArgumentException();
    }

    public function runtimeException(): void
    {
        throw new \RuntimeException();
    }
}
