<?php

declare(strict_types=1);

namespace Test;

use Foo\Bar;

class NamespacedNames
{
    public function qualified(): void
    {
        throw new Bar\RuntimeException();
    }

    public function sameNamespace(): void
    {
        throw new Exception();
    }

    public function relative(): void
    {
        throw new namespace\LogicException();
    }

    public function caught(): void
    {
        try {
        } catch (\Exception $e) {
        }
    }

    public function lowercase(): void
    {
        throw new \runtimeexception();
    }

    public function throwExpression(): void
    {
        $fn = static fn () => throw new \LogicException();
    }
}
