brainbits Coding Standard
=========================

[PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) ruleset used at brainbits.
It is based on the [Doctrine Coding Standard](https://github.com/doctrine/coding-standard)
(which includes the [Slevomat Coding Standard](https://github.com/slevomat/coding-standard)),
relaxes a few of its rules and adds a custom sniff.

Requires PHP 8.4 or newer and PHP_CodeSniffer 4. The name of the standard is `Brainbits`.

Differences to Doctrine
=======================

Disabled rules:

- `Generic.Formatting.MultipleStatementAlignment.NotSame` – assignments don't need to be aligned
- `Generic.Formatting.SpaceAfterNot.Incorrect` – no space required after `!`
- `Squiz.Arrays.ArrayDeclaration.MultiLineNotAllowed` – multi-line arrays with a single item are allowed

Additional rules:

- `Brainbits.Exception.GlobalException` – forbids `throw new` with PHP's built-in global
  exceptions (`\Exception`, `\ErrorException` and the SPL exceptions like `\RuntimeException`,
  `\InvalidArgumentException`, …). Only direct `throw new …` statements are checked.
  Extend the exceptions in your own namespace instead:

  ```php
  // error
  throw new \RuntimeException('Not found');

  // ok
  final class NotFound extends \RuntimeException {}
  throw new NotFound('Not found');
  ```

How to Use
==========

Global
------

1. Clone repository
2. Install dependencies
   ```bash
   composer install
   ```
3. Configure standard: 
   ```bash
   {install-dir}/vendor/bin/phpcs --config-set default_standard Brainbits
   
   ```
4. Execute:
   ```bash
   {install-dir}/vendor/bin/phpcs {src-dir}
   ```

In Project
----------

1. Add the standard to your project:
   ```bash
   composer require brainbits/phpcs-standard
   ```
2. Configure standard:
   ```bash
   vendor/bin/phpcs --config-set default_standard Brainbits
   ```
3. Execute:
   ```bash
   vendor/bin/phpcs {src-dir}
   ```

In Project with Ruleset Customization
-------------------------------------

1. Add the standard to your project:
   ```bash
   composer require brainbits/phpcs-standard
   ```
2. Create phpcs.xml (see https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Advanced-Usage)
   1. Include brainbits ruleset: 
      ```xml
      <rule ref="Brainbits" />
      ```
   2. Add default src-dir: 
      ```xml
      <file>{src-dir}</file>
      ```
   3. Tweak to your needs, for example add rules or disable rules included in the brainbits standard.
3. Execute:
   ```bash
   vendor/bin/phpcs
   ```

Used Code Styles
================
- Slevomat https://github.com/slevomat/coding-standard
- Doctrine https://github.com/doctrine/coding-standard

Development
===========

```bash
composer install
vendor/bin/phpunit                  # unit tests of the custom sniffs
vendor/bin/phpcs                    # check this repository with its own standard
vendor/bin/phpcs --basepath=. --report=summary --report-file=phpcs.log --standard=Brainbits `find tests/Brainbits/input/* | sort`; diff -u tests/Brainbits/expected_report.txt phpcs.log   # ruleset integration test
```

License
=======

MIT, see [LICENSE](LICENSE).
