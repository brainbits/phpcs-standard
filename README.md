How to Use
==========

Global
------

1. Clone repository
2. composer install
3. Configure standard:
   <install-dir>/vendor/bin/phpcs --standard BrainbitsCodingStandard <src-dir> 

In Project
----------

1. composer require brainbits/php-code-style
2. vendor/bin/phpcs --standard BrainbitsCodingStandard <src-dir>

Docker
------

1. docker run -it --rm -v $PWD:/app service-phpcs <src-dir>

Used Code Styles
================
- Symfony2 https://github.com/djoos/Symfony2-coding-standard
- Slevomat https://github.com/slevomat/coding-standard

