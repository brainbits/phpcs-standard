<?php

declare(strict_types=1);

namespace Test;

class ExceptionInAnnotation
{
    /**
     * @expectedException \Exception
     */
    public function baz(): void
    {
    }
}
