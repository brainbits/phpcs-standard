<?php

declare(strict_types=1);

namespace A;

use Exception;

throw new Exception();

namespace B;

use RuntimeException as Foo;

throw new Exception();
throw new Foo();
