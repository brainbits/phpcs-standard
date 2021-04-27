<?php

declare(strict_types=1);

namespace BrainbitsCodingStandardTest\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use const T_OPEN_TAG;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint

class DeprecatedStandardSniff implements Sniff
{
    public const CODE_DEPRECATED_STANDARD = 'DeprecatedStandard';

    /** @var bool */
    private $handled = false;

    /**
     * @return mixed[]
     */
    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    /**
     * @param int $openTagPointer
     */
    public function process(File $phpcsFile, $openTagPointer): void
    {
        if ($this->handled) {
            return;
        }

        $phpcsFile->addWarning(
            'The standard BrainbitsCodingStandard is deprecated. Use standard Brainbits instead',
            $openTagPointer,
            self::CODE_DEPRECATED_STANDARD
        );

        $this->handled = true;
    }
}
