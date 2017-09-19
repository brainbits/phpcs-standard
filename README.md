How to Use
==========

Global
------

1. Clone repository
2. composer install
3. Configure standard: 
   &lt;install-dir&gt;/vendor/bin/phpcs --config-set default_standard BrainbitsCodingStandard
4. Ausführen:
   &lt;install-dir&gt;/vendor/bin/phpcs &lt;src-dir&gt;

In Project
----------

1. composer require brainbits/php-code-style
2. vendor/bin/phpcs --config-set default_standard BrainbitsCodingStandard

In Project with Custom Ruleset
------------------------------

1. composer require brainbits/php-code-style
2. Create phpcs.xml (https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage)
3. vendor/bin/phpcs

Docker
------

1. docker run -it --rm -v $PWD:/app brainbits/phpcs &lt;src-dir&gt;

Used Code Styles
================
- Symfony2 https://github.com/djoos/Symfony2-coding-standard
- Slevomat https://github.com/slevomat/coding-standard

