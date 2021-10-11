# Contributing

Thank you for your interest in contributing! Before proceeding, please read the [Code of Conduct](CODE_OF_CONDUCT.md).

## Developing

[Composer](https://getcomposer.org) and [NPM](https://www.npmjs.com) will both be required to develop & test the library. Once they are configured, run `composer install` and `npm install` from the repository root directory to install the required dependencies.

Code is automatically formatted by [Prettier](https://prettier.io). Formatting can be initiated manually with the command `composer format`. See [the Prettier docs](https://prettier.io/docs/en/watching-files.html) to enable file watching & live reformatting.

Static type checking is performed by [Psalm](https://psalm.dev). Additional [Psalm docblock annotations](https://psalm.dev/docs/annotating_code/supported_annotations/) should be added where necessary to improve type checking. Type analysis & verification can be performed with the command `composer analyze`.

Unit tests are managed by [PHPUnit](https://phpunit.de). Tests can be run with the command `composer test`.

## Submitting Changes

Before submitting changes, please ensure all code formatting passes validation with the command `composer format-check`, static analysis passes with the command `composer analyze`, and all tests pass with the command `composer test`.

To submit changes, please [open a pull request](https://docs.github.com/en/github/collaborating-with-pull-requests).

Thank you for your contribution!
