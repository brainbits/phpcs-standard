<?php

declare(strict_types = 1);

namespace BrainbitsCodingStandard\Sniffs;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
	/**
	 * @param string $filePath
	 * @param mixed[] $sniffProperties
	 * @param string[] $codesToCheck
	 * @return File
	 */
	protected function checkFile(string $filePath, array $sniffProperties = [], array $codesToCheck = []): File
	{
		$codeSniffer = new Runner();
		$codeSniffer->config = new Config([
			'-s',
		]);
		$codeSniffer->init();

		if (count($sniffProperties) > 0) {
			$codeSniffer->ruleset->ruleset[$this->getSniffName()]['properties'] = $sniffProperties;
		}

		$codeSniffer->ruleset->sniffs = [$this->getSniffClassName() => $this->getSniffClassName()];

		if (count($codesToCheck) > 0) {
			foreach ($this->getSniffClassReflection()->getConstants() as $constantName => $constantValue) {
				if (strpos($constantName, 'CODE_') === 0 && !in_array($constantValue, $codesToCheck, true)) {
					$codeSniffer->ruleset->ruleset[sprintf('%s.%s', $this->getSniffName(), $constantValue)]['severity'] = 0;
				}
			}
		}

		$codeSniffer->ruleset->populateTokenListeners();

		$file = new LocalFile($filePath, $codeSniffer->ruleset, $codeSniffer->config);
		$file->process();

		return $file;
	}

	protected function assertNoSniffErrorInFile(File $file)
	{
		$errors = $file->getErrors();
		$this->assertEmpty($errors, sprintf('No errors expected, but %d errors found.', count($errors)));
	}

	protected function assertSniffError(File $codeSnifferFile, int $line, string $code, string $message = null)
	{
		$errors = $codeSnifferFile->getErrors();
		$this->assertTrue(isset($errors[$line]), sprintf('Expected error on line %s, but none found.', $line));

		$sniffCode = sprintf('%s.%s', $this->getSniffName(), $code);

		$this->assertTrue(
			$this->hasError($errors[$line], $sniffCode, $message),
			sprintf(
				'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
				$sniffCode,
				$message !== null ? sprintf(' with message "%s"', $message) : '',
				$line,
				PHP_EOL . PHP_EOL,
				$line,
				PHP_EOL,
				$this->getFormattedErrors($errors[$line]),
				PHP_EOL
			)
		);
	}

	protected function assertNoSniffError(File $codeSnifferFile, int $line)
	{
		$errors = $codeSnifferFile->getErrors();
		$this->assertFalse(
			isset($errors[$line]),
			sprintf(
				'Expected no error on line %s, but found:%s%s%s',
				$line,
				PHP_EOL . PHP_EOL,
				isset($errors[$line]) ? $this->getFormattedErrors($errors[$line]) : '',
				PHP_EOL
			)
		);
	}

	protected function assertAllFixedInFile(File $codeSnifferFile)
	{
		$codeSnifferFile->fixer->fixFile();

		$this->assertStringEqualsFile(preg_replace('~(\\.php)$~', '.fixed\\1', $codeSnifferFile->getFilename()), $codeSnifferFile->fixer->getContents());
	}

	/**
	 * @param mixed[][][] $errorsOnLine
	 * @param string $sniffCode
	 * @param string|null $message
	 * @return bool
	 */
	private function hasError(array $errorsOnLine, string $sniffCode, string $message = null): bool
	{
		foreach ($errorsOnLine as $errorsOnPosition) {
			foreach ($errorsOnPosition as $error) {
				if (
					$error['source'] === $sniffCode
					&& ($message === null || strpos($error['message'], $message) !== false)
				) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param mixed[][][] $errors
	 */
	private function getFormattedErrors(array $errors): string
	{
		return implode(PHP_EOL, array_map(function (array $errors): string {
			return implode(PHP_EOL, array_map(function (array $error): string {
				return sprintf("\t%s: %s", $error['source'], $error['message']);
			}, $errors));
		}, $errors));
	}

	protected function getSniffName(): string
	{
		return preg_replace(
			[
				'~\\\~',
				'~\.Sniffs~',
				'~Sniff$~',
			],
			[
				'.',
				'',
				'',
			],
			$this->getSniffClassName()
		);
	}

	protected function getSniffClassName(): string
	{
		return substr(get_class($this), 0, -strlen('Test'));
	}

	protected function getSniffClassReflection(): \ReflectionClass
	{
		static $reflection;

		if ($reflection === null) {
			$reflection = new \ReflectionClass($this->getSniffClassName());
		}

		return $reflection;
	}
}
