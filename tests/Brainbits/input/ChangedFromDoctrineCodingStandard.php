<?php

declare(strict_types=1);

namespace Test;

class ChangedFromDoctrineCodingStandard
{
    /**
     * Generic.Formatting.MultipleStatementAlignment.NotSame
     */
    public static FOO = 1;
    public static SOME_CONSTANT = 1;
    public static AND_ANOTHER = 1;

    /**
     * Generic.Formatting.SpaceAfterNot.Incorrect
     */
    public function test(): void
    {
        if (!true) {
            return;
        }
    }
}
