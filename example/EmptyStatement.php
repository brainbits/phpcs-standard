<?php

declare(strict_types = 1);

namespace Test;

class EmptyStatement
{
    public function emptyWhile()
    {
        while (true) {
        }
    }
    public function emptyForeach()
    {
        foreach ([] as $i) {
        }
    }
    public function emptyFor()
    {
        for ($i=0; $i<10; $i++) {
        }
    }
}
