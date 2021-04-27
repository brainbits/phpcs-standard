<?php

declare(strict_types=1);

namespace Brainbits\Sniffs\Exception;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

use function defined;
use function in_array;
use function sprintf;
use function substr;

use const T_NS_SEPARATOR;
use const T_OPEN_TAG;
use const T_STRING;
use const T_THROW;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint

/**
 * Global exception sniff
 */
class GlobalExceptionSniff implements Sniff
{
    public const CODE_GLOBAL_EXCEPTION = 'GlobalException';

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
        $currentPointer = $openTagPointer;

        $globalExceptions = [
            'BadFunctionCallException',
            'BadMethodCallException',
            'DomainException',
            'ErrorException',
            'Exception',
            'InvalidArgumentException',
            'LengthException',
            'LogicException',
            'OutOfBoundsException',
            'OutOfRangeException',
            'OverflowException',
            'RangeException',
            'RuntimeException',
            'UnderflowException',
            'UnexpectedValueException',
        ];

        $useStatements = UseStatementHelper::getFileUseStatements($phpcsFile);

        if (defined('T_NAME_FULLY_QUALIFIED')) {
            // php 8.x
            // phpcs:ignore SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFallbackGlobalName
            $classTokens = [T_NAME_FULLY_QUALIFIED, T_NS_SEPARATOR, T_STRING];
        } else {
            $classTokens = [T_NS_SEPARATOR, T_STRING];
        }

        do {
            $currentPointer = TokenHelper::findNext($phpcsFile, T_THROW, $currentPointer + 1);
            if (!$currentPointer) {
                break;
            }

            $classStartPointer = TokenHelper::findNext($phpcsFile, $classTokens, $currentPointer + 1);
            if (!$classStartPointer) {
                continue;
            }

            $classEndPointer = TokenHelper::findNextExcluding(
                $phpcsFile,
                $classTokens,
                $classStartPointer + 1
            ) - 1;
            if (!$classEndPointer) {
                continue;
            }

            $class = TokenHelper::getContent($phpcsFile, $classStartPointer, $classEndPointer);

            if (substr($class, 0, 1) === '\\') {
                // fully qualfied
                $class = substr($class, 1);
                if (in_array($class, $globalExceptions)) {
                    $phpcsFile->addError(
                        sprintf('Global exception "%s" used. It should be locally extended.', $class),
                        $currentPointer,
                        self::CODE_GLOBAL_EXCEPTION
                    );
                }
            } else {
                // referenced
                foreach ($useStatements as $useStatementX) {
                    foreach ($useStatementX as $useStatement) {
                        // phpcs:ignore
                        if ($useStatement->getNameAsReferencedInFile() === $class || $useStatement->getFullyQualifiedTypeName() === $class) {
                            if (in_array($useStatement->getFullyQualifiedTypeName(), $globalExceptions)) {
                                $phpcsFile->addError(
                                    sprintf('Global exception "%s" used. It should be locally extended.', $class),
                                    $classStartPointer,
                                    self::CODE_GLOBAL_EXCEPTION
                                );
                            }
                        }
                    }
                }
            }

            $currentPointer = $classEndPointer + 1;
        } while ($currentPointer);
    }
}
