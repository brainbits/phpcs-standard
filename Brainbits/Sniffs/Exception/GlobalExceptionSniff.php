<?php

declare(strict_types=1);

namespace Brainbits\Sniffs\Exception;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

use function in_array;
use function ltrim;
use function sprintf;
use function str_starts_with;
use function strlen;
use function strtolower;
use function substr;

use const T_NAME_FULLY_QUALIFIED;
use const T_NAME_QUALIFIED;
use const T_NAME_RELATIVE;
use const T_NEW;
use const T_STRING;
use const T_THROW;

/**
 * Forbids throwing global SPL exceptions, they should be extended locally.
 */
class GlobalExceptionSniff implements Sniff
{
    public const string CODE_GLOBAL_EXCEPTION = 'GlobalException';

    private const array GLOBAL_EXCEPTIONS = [
        'badfunctioncallexception',
        'badmethodcallexception',
        'domainexception',
        'errorexception',
        'exception',
        'invalidargumentexception',
        'lengthexception',
        'logicexception',
        'outofboundsexception',
        'outofrangeexception',
        'overflowexception',
        'rangeexception',
        'runtimeexception',
        'underflowexception',
        'unexpectedvalueexception',
    ];

    private const string RELATIVE_PREFIX = 'namespace\\';

    /** @return list<int|string> */
    public function register(): array
    {
        return [T_THROW];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $newPointer = TokenHelper::findNextEffective($phpcsFile, $stackPtr + 1);
        if ($newPointer === null || $phpcsFile->getTokens()[$newPointer]['code'] !== T_NEW) {
            // e.g. `throw $exception;` or `throw $this->createException();`
            return;
        }

        $classPointer = TokenHelper::findNextEffective($phpcsFile, $newPointer + 1);
        $nameTokens = [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE];
        if ($classPointer === null || !in_array($phpcsFile->getTokens()[$classPointer]['code'], $nameTokens, true)) {
            // e.g. `throw new $class();` or `throw new class extends Foo {};`
            return;
        }

        $className = $this->resolveClassName($phpcsFile, $classPointer);
        if (!in_array(strtolower($className), self::GLOBAL_EXCEPTIONS, true)) {
            return;
        }

        $phpcsFile->addError(
            sprintf('Global exception "%s" used. It should be locally extended.', $className),
            $classPointer,
            self::CODE_GLOBAL_EXCEPTION,
        );
    }

    /**
     * Returns the fully qualified class name without leading backslash.
     */
    private function resolveClassName(File $phpcsFile, int $classPointer): string
    {
        $name = $phpcsFile->getTokens()[$classPointer]['content'];

        if (str_starts_with(strtolower($name), self::RELATIVE_PREFIX)) {
            $namespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $classPointer);
            $name = substr($name, strlen(self::RELATIVE_PREFIX));

            return $namespace === null ? $name : $namespace . '\\' . $name;
        }

        return ltrim(NamespaceHelper::resolveClassName($phpcsFile, $name, $classPointer), '\\');
    }
}
