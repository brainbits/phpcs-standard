<?php

declare(strict_types=1);

namespace Test;

class ChangedFromDoctrineCodingStandard
{
    /**
     * Generic.Formatting.MultipleStatementAlignment.NotSame
     */
    public function alignment(): int
    {
        $foo = 1;
        $someVariable = 2;
        $andAnother = 3;

        return $foo + $someVariable + $andAnother;
    }

    /**
     * Generic.Formatting.SpaceAfterNot.Incorrect
     */
    public function spaceAfterNot(bool $flag): bool
    {
        return !$flag;
    }

    /**
     * Squiz.Arrays.ArrayDeclaration.MultiLineNotAllowed
     *
     * @return list<string>
     */
    public function multiLineArrayWithSingleItem(): array
    {
        return [
            'a',
        ];
    }
}
