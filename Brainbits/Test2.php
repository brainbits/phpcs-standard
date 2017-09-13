<?php

declare(strict_types = 1);

namespace Brainbits;

/**
 * Class Test2
 */
class Test2
{
    /**
     * @param string[] $list
     */
    public function foo(string $test, array $list): void
    {
        echo 'foo'.$test.json_encode($list);
    }

    /**
     * @return string[]
     */
    public function bar(): array
    {
        return [];
    }
}
