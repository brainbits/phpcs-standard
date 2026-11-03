<?php

declare(strict_types=1);

namespace A {
    use Exception;

    throw new Exception();
}

namespace B {
    throw new Exception();
}

namespace {
    throw new LogicException();
    throw new namespace\RuntimeException();
    throw new Foo();
}
