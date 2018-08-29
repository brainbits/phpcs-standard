<?php

declare(strict_types = 1);

namespace Test;

class BlankLineBeforeReturn
{
    public function shortSyntax()
    {
        if (true) {
            return false;
        }
        return true;
    }
}
