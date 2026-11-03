<?php

declare(strict_types=1);

namespace Brainbits\Sniffs\Exception;

use SlevomatCodingStandard\Sniffs\TestCase;

final class GlobalExceptionSniffTest extends TestCase
{
    public function testNoNamespace(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/NoNamespace.php');

        self::assertNoSniffErrorInFile($report);
    }

    public function testNoException(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/NoException.php');

        self::assertNoSniffErrorInFile($report);
    }

    public function testExceptionInAnnotation(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/ExceptionInAnnotation.php');

        self::assertNoSniffErrorInFile($report);
    }

    public function testAllowedExceptions(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/AllowedExceptions.php');

        self::assertNoSniffErrorInFile($report);
    }

    public function testExceptionInUseWithoutAlias(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/ExceptionInUseWithoutAlias.php');

        self::assertSame(3, $report->getErrorCount());
        self::assertSniffError($report, 15, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, 'LogicException');
        self::assertSniffError($report, 20, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, 'RuntimeException');
        self::assertSniffError($report, 25, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, '"Exception"');
    }

    public function testExceptionInUseWithAlias(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/ExceptionInUseWithAlias.php');

        self::assertSame(2, $report->getErrorCount());
        self::assertSniffError($report, 14, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, '"Exception"');
        self::assertSniffError($report, 19, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, 'RuntimeException');
    }

    public function testFullQualifiedException(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/FullQualifiedException.php');

        self::assertSame(3, $report->getErrorCount());
        self::assertSniffError($report, 11, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, '"Exception"');
        self::assertSniffError($report, 16, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, 'InvalidArgumentException');
        self::assertSniffError($report, 21, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, 'RuntimeException');
    }

    public function testUnqualifiedExceptionInGlobalNamespace(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/GlobalNamespace.php');

        self::assertSame(1, $report->getErrorCount());
        self::assertSniffError($report, 9, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, '"Exception"');
    }

    public function testThrowWithoutNewIsIgnored(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/ThrowWithoutNew.php');

        self::assertNoSniffErrorInFile($report);
    }

    public function testNamespacedNames(): void
    {
        $report = self::checkFile(__DIR__ . '/fixture/NamespacedNames.php');

        self::assertSame(2, $report->getErrorCount());
        self::assertSniffError($report, 35, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, 'runtimeexception');
        self::assertSniffError($report, 40, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION, 'LogicException');
    }
}
