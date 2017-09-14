<?php

declare(strict_types = 1);

namespace Test;

class exceptionInAnnotation
{
    /**
     * @expectedException \Exception
     */
    public function baz(): void
    {
    }
}
