<?php

declare(strict_types=1);

namespace Test;

use Exception as Foo;

class ThrowExpressions
{
    public function expressions(?string $value, int $type): void
    {
        throw (new \LogicException());
        $value ?? throw new \RuntimeException();
        $value ?: throw new \UnexpectedValueException();
        match ($type) {
            1 => 'one',
            default => throw new \DomainException(),
        };
        throw /* comment */ new /* comment */ \Exception();
        throw new foo();
        throw new \Exception;
    }

    public function ignored(): void
    {
        throw new class extends \Exception {
        };
        throw new static();
        throw \RuntimeException::create();
    }
}
