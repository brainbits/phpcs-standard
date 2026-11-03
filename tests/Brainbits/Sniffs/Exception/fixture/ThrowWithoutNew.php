<?php

declare(strict_types=1);

namespace Test;

use RuntimeException;

class ThrowWithoutNew
{
    public function rethrow(\Throwable $e): void
    {
        throw $e;
    }

    public function factory(): void
    {
        throw $this->createException();
    }

    public function dynamic(string $class): void
    {
        throw new $class();
    }
}

$x = static fn ($e) => throw $e;
$y = RuntimeException::class;
