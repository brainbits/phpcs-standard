<?php

declare(strict_types=1);

namespace Brainbits\Sniffs\Exception;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

use function explode;
use function in_array;
use function ltrim;
use function preg_match;
use function preg_replace;
use function preg_split;
use function rtrim;
use function sprintf;
use function strlen;
use function strpos;
use function strrpos;
use function strtolower;
use function substr;
use function trim;

use const T_COMMENT;
use const T_DOC_COMMENT;
use const T_NAME_FULLY_QUALIFIED;
use const T_NAME_QUALIFIED;
use const T_NAME_RELATIVE;
use const T_NAMESPACE;
use const T_NEW;
use const T_OPEN_PARENTHESIS;
use const T_SEMICOLON;
use const T_STRING;
use const T_THROW;
use const T_USE;
use const T_WHITESPACE;

/**
 * Forbids throwing the built-in global exceptions, they should be extended locally.
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

    private const array NAME_TOKENS = [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE];

    /** @return list<int|string> */
    public function register(): array
    {
        return [T_THROW];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // skip parentheses, e.g. `throw (new Exception());`
        $newPointer = TokenHelper::findNextEffective($phpcsFile, $stackPtr + 1);
        while ($newPointer !== null && $tokens[$newPointer]['code'] === T_OPEN_PARENTHESIS) {
            $newPointer = TokenHelper::findNextEffective($phpcsFile, $newPointer + 1);
        }

        if ($newPointer === null || $tokens[$newPointer]['code'] !== T_NEW) {
            // e.g. `throw $exception;` or `throw $this->createException();`
            return;
        }

        $classPointer = TokenHelper::findNextEffective($phpcsFile, $newPointer + 1);
        if ($classPointer === null || !in_array($tokens[$classPointer]['code'], self::NAME_TOKENS, true)) {
            // e.g. `throw new $class();` or `throw new class extends Foo {};`
            return;
        }

        $className = $this->resolveClassName($phpcsFile, $classPointer);
        if (!in_array(strtolower($className), self::GLOBAL_EXCEPTIONS, true)) {
            return;
        }

        $phpcsFile->addError(
            sprintf('Global exception "%s" used. It should be locally extended.', $className),
            $stackPtr,
            self::CODE_GLOBAL_EXCEPTION,
        );
    }

    /**
     * Returns the fully qualified class name without leading backslash.
     */
    private function resolveClassName(File $phpcsFile, int $classPointer): string
    {
        $name = $phpcsFile->getTokens()[$classPointer]['content'];

        if ($name[0] === '\\') {
            return ltrim($name, '\\');
        }

        $namespacePointer = $this->findNamespacePointer($phpcsFile, $classPointer);
        $namespace = $namespacePointer === null ? '' : $this->getNamespaceName($phpcsFile, $namespacePointer);

        if (strtolower(substr($name, 0, strlen(self::RELATIVE_PREFIX))) === self::RELATIVE_PREFIX) {
            return $this->prefixNamespace($namespace, substr($name, strlen(self::RELATIVE_PREFIX)));
        }

        $separatorPosition = strpos($name, '\\');
        $firstPart = $separatorPosition === false ? $name : substr($name, 0, $separatorPosition);
        $rest = $separatorPosition === false ? '' : substr($name, $separatorPosition);

        $imports = $this->getClassImports($phpcsFile, $namespacePointer, $classPointer);
        $import = $imports[strtolower($firstPart)] ?? null;
        if ($import !== null) {
            return $import . $rest;
        }

        return $this->prefixNamespace($namespace, $name);
    }

    /**
     * Returns the pointer of the namespace declaration the given pointer belongs to,
     * null for code outside of any namespace declaration.
     */
    private function findNamespacePointer(File $phpcsFile, int $pointer): int|null
    {
        $tokens = $phpcsFile->getTokens();

        // bracketed syntax: `namespace Foo { ... }` or `namespace { ... }`
        foreach ($tokens[$pointer]['conditions'] as $conditionPointer => $conditionCode) {
            if ($conditionCode === T_NAMESPACE) {
                return $conditionPointer;
            }
        }

        // unbracketed syntax: `namespace Foo;`
        $namespacePointer = $pointer;
        do {
            $namespacePointer = TokenHelper::findPrevious($phpcsFile, T_NAMESPACE, $namespacePointer - 1);
        } while (
            $namespacePointer !== null
            && (
                isset($tokens[$namespacePointer]['scope_closer'])
                || $tokens[$namespacePointer]['conditions'] !== []
            )
        );

        return $namespacePointer;
    }

    private function getNamespaceName(File $phpcsFile, int $namespacePointer): string
    {
        $tokens = $phpcsFile->getTokens();
        $namePointer = TokenHelper::findNextEffective($phpcsFile, $namespacePointer + 1);

        if ($namePointer === null || !in_array($tokens[$namePointer]['code'], [T_STRING, T_NAME_QUALIFIED], true)) {
            // `namespace { ... }` is the global namespace
            return '';
        }

        return $tokens[$namePointer]['content'];
    }

    /**
     * Returns the class imports (lower case alias => fully qualified name) of the namespace
     * the given pointer belongs to.
     *
     * @return array<string, string>
     */
    private function getClassImports(File $phpcsFile, int|null $namespacePointer, int $pointer): array
    {
        $tokens = $phpcsFile->getTokens();
        $expectedConditions = $namespacePointer !== null && isset($tokens[$namespacePointer]['scope_closer'])
            ? [$namespacePointer => T_NAMESPACE]
            : [];

        $imports = [];
        $usePointer = $namespacePointer ?? 0;
        while (true) {
            $usePointer = TokenHelper::findNext($phpcsFile, T_USE, $usePointer + 1, $pointer);
            if ($usePointer === null) {
                break;
            }

            if ($tokens[$usePointer]['conditions'] !== $expectedConditions) {
                // trait use or import of another namespace
                continue;
            }

            $nextPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);
            if ($nextPointer === null || $tokens[$nextPointer]['code'] === T_OPEN_PARENTHESIS) {
                // closure use
                continue;
            }

            $endPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $usePointer + 1);
            if ($endPointer === null) {
                break;
            }

            $imports = [...$imports, ...$this->parseUseStatement($phpcsFile, $usePointer + 1, $endPointer - 1)];
            $usePointer = $endPointer;
        }

        return $imports;
    }

    /**
     * Parses `Foo\Bar`, `Foo\Bar as Baz, Qux` and group uses like `Foo\{Bar, Baz as Qux}`.
     *
     * @return array<string, string>
     */
    private function parseUseStatement(File $phpcsFile, int $startPointer, int $endPointer): array
    {
        $tokens = $phpcsFile->getTokens();
        $statement = '';
        for ($pointer = $startPointer; $pointer <= $endPointer; $pointer++) {
            if (in_array($tokens[$pointer]['code'], [T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            $statement .= $tokens[$pointer]['code'] === T_WHITESPACE ? ' ' : $tokens[$pointer]['content'];
        }

        $statement = trim((string) preg_replace('/\s+/', ' ', $statement));
        if ($this->isFunctionOrConstImport($statement)) {
            return [];
        }

        $prefix = '';
        $groupStart = strpos($statement, '{');
        if ($groupStart !== false) {
            $prefix = rtrim(trim(substr($statement, 0, $groupStart)), '\\') . '\\';
            $statement = rtrim(substr($statement, $groupStart + 1), '} ');
        }

        $imports = [];
        foreach (explode(',', $statement) as $entry) {
            $entry = trim($entry);
            if ($entry === '' || $this->isFunctionOrConstImport($entry)) {
                continue;
            }

            $parts = (array) preg_split('/\s+as\s+/i', $entry);
            $fullyQualifiedName = ltrim($prefix . trim((string) $parts[0]), '\\');
            $alias = isset($parts[1]) ? trim((string) $parts[1]) : $this->getLastNamePart($fullyQualifiedName);

            $imports[strtolower($alias)] = $fullyQualifiedName;
        }

        return $imports;
    }

    private function isFunctionOrConstImport(string $statement): bool
    {
        return preg_match('/^(function|const)\s/i', $statement) === 1;
    }

    private function getLastNamePart(string $name): string
    {
        $separatorPosition = strrpos($name, '\\');

        return $separatorPosition === false ? $name : substr($name, $separatorPosition + 1);
    }

    private function prefixNamespace(string $namespace, string $name): string
    {
        return $namespace === '' ? $name : $namespace . '\\' . $name;
    }
}
