<?php

declare(strict_types = 1);

namespace Brainbits\Sniffs\Exception;

use SlevomatCodingStandard\Sniffs\TestCase;

class GlobalExceptionSniffTest extends TestCase
{
    public function testNoNamespace()
    {
        $report = $this->checkFile(__DIR__.'/fixture/NoNamespace.php');

        $this->assertNoSniffErrorInFile($report);
    }

    public function testNoException()
    {
        $report = $this->checkFile(__DIR__.'/fixture/NoException.php');

        $this->assertNoSniffErrorInFile($report);
    }

    public function testExceptionInAnnotation()
    {
        $report = $this->checkFile(__DIR__.'/fixture/ExceptionInAnnotation.php');

        $this->assertNoSniffErrorInFile($report);
    }

    public function testAllowedExceptions()
    {
        $report = $this->checkFile(__DIR__.'/fixture/AllowedExceptions.php');

        $this->assertNoSniffErrorInFile($report);
    }

    public function testExceptionInUseWithoutAlias()
    {
        $report = $this->checkFile(__DIR__.'/fixture/ExceptionInUseWithoutAlias.php');

        $this->assertSame(3, $report->getErrorCount());
        $this->assertSniffError($report, 15, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 20, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 25, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
    }

    public function testExceptionInUseWithAlias()
    {
        $report = $this->checkFile(__DIR__.'/fixture/ExceptionInUseWithAlias.php');

        $this->assertSame(2, $report->getErrorCount());
        $this->assertSniffError($report, 14, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 19, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
    }

    public function testFullQualifiedException()
    {
        $report = $this->checkFile(__DIR__.'/fixture/FullQualifiedException.php');

        $this->assertSame(3, $report->getErrorCount());
        $this->assertSniffError($report, 11, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 16, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 21, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
    }
}