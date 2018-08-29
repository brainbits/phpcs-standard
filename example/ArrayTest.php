<?php

declare(strict_types = 1);

namespace Test;

class ArrayTest
{
    public function shortSyntax()
    {
        return array('test');
    }

    public function lastComma()
    {
        return [
            'a',
            'b'
        ];
    }

    public function indent()
    {
        return [
          'a',
        ];
    }
}
