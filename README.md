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
   {install-dir}/vendor/bin/phpcs --config-set default_standard BrainbitsCodingStandard
   
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
   vendor/bin/phpcs --config-set default_standard BrainbitsCodingStandard
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
2. Create phpcs.xml (See https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage)
   1. Include brainbits ruleset: 
      ```xml
      <rule ref="BrainbitsCodingStandard" />
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

Docker
------

1. docker run -it --rm -v $PWD:/app brainbits/phpcs-standard {src-dir}

Used Code Styles
================
- Symfony2 https://github.com/djoos/Symfony2-coding-standard
- Slevomat https://github.com/slevomat/coding-standard

