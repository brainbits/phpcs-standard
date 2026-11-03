<?php

declare(strict_types=1);

class GlobalNamespace
{
    public function exception(): void
    {
        throw new Exception();
    }

    public function localException(): void
    {
        throw new \Foo\Exception();
    }
}
