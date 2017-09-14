<?php

declare(strict_types = 1);

namespace BrainbitsCodingStandard\Sniffs\Exceptions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

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
        return [
            T_OPEN_TAG,
        ];
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
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

        $useStatements = UseStatementHelper::getUseStatements($phpcsFile, $currentPointer);

        do {
            $currentPointer = TokenHelper::findNext($phpcsFile, T_THROW, $currentPointer + 1);
            if (!$currentPointer) {
                break;
            }

            $classStartPoint = TokenHelper::findNext($phpcsFile, [T_NS_SEPARATOR, T_STRING], $currentPointer + 1);
            $classEndPointer = TokenHelper::findNextExcluding($phpcsFile, [T_NS_SEPARATOR, T_STRING], $classStartPoint + 1) - 1;
            $class = TokenHelper::getContent($phpcsFile, $classStartPoint, $classEndPointer);

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
                foreach ($useStatements as $useStatement) {
                    if ($useStatement->getNameAsReferencedInFile() === $class || $useStatement->getFullyQualifiedTypeName() === $class) {
                        if (in_array($useStatement->getFullyQualifiedTypeName(), $globalExceptions)) {
                            $phpcsFile->addError(
                                sprintf('Global exception "%s" used. It should be locally extended.', $class),
                                $classStartPoint,
                                self::CODE_GLOBAL_EXCEPTION
                            );
                        }
                    }
                }
            }

            $currentPointer = $classEndPointer + 1;
        } while ($currentPointer);
    }
}
