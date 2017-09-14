<?php declare(strict_types = 1);

namespace BrainbitsCodingStandard\Sniffs\Exceptions;

use BrainbitsCodingStandard\Sniffs\TestCase;

class GlobalExceptionSniffTest extends TestCase
{
    public function testNoNamespace()
    {
        $report = $this->checkFile(__DIR__.'/data/noNamespace.php');

        $this->assertNoSniffErrorInFile($report);
    }

    public function testNoException()
    {
        $report = $this->checkFile(__DIR__.'/data/noException.php');

        $this->assertNoSniffErrorInFile($report);
    }

    public function testExceptionInAnnotation()
    {
        $report = $this->checkFile(__DIR__.'/data/exceptionInAnnotation.php');

        $this->assertNoSniffErrorInFile($report);
    }

    public function testAllowedExceptions()
    {
        $report = $this->checkFile(__DIR__.'/data/allowedExceptions.php');

        $this->assertNoSniffErrorInFile($report);
    }

    public function testExceptionInUseWithoutAlias()
    {
        $report = $this->checkFile(__DIR__.'/data/exceptionInUseWithoutAlias.php');

        $this->assertSame(3, $report->getErrorCount());
        $this->assertSniffError($report, 15, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 20, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 25, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
    }

    public function testExceptionInUseWithAlias()
    {
        $report = $this->checkFile(__DIR__.'/data/exceptionInUseWithAlias.php');

        $this->assertSame(2, $report->getErrorCount());
        $this->assertSniffError($report, 14, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 19, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
    }

    public function testFullQualifiedException()
    {
        $report = $this->checkFile(__DIR__.'/data/fullQualifiedException.php');

        $this->assertSame(3, $report->getErrorCount());
        $this->assertSniffError($report, 11, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 16, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
        $this->assertSniffError($report, 21, GlobalExceptionSniff::CODE_GLOBAL_EXCEPTION);
    }
}
