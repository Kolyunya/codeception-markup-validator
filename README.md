# Codeception Markup Validator

## Status
[![Build Status](https://travis-ci.org/Kolyunya/codeception-markup-validator.svg?branch=master)](https://travis-ci.org/Kolyunya/codeception-markup-validator)
[![Coverage Status](https://img.shields.io/coveralls/Kolyunya/codeception-markup-validator/master.svg)](https://coveralls.io/github/Kolyunya/codeception-markup-validator?branch=master)
[![Code Climate](https://codeclimate.com/github/Kolyunya/codeception-markup-validator/badges/gpa.svg)](https://codeclimate.com/github/Kolyunya/codeception-markup-validator)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![Latest Stable Version](https://poser.pugx.org/kolyunya/codeception-markup-validator/v/stable)](https://packagist.org/packages/kolyunya/codeception-markup-validator)

## Description
Markup validator module for Codeception.

## Installation
The recommended way of package installation is via composer:
```sh
composer require --dev kolyunya/codeception-markup-validator
```

## Usage
Add the module to you acceptance suit configuration:
```yaml
class_name: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: 'http://localhost/'
        - Kolyunya\Codeception\Module\MarkupValidator
```

Use it in your acceptance tests like this:
```php
$I->validateMarkup();
```

## Configuration
The module does not require any configuration. The default setup will work if you have either [`PhpBrowser`](https://github.com/Codeception/Codeception/blob/2.2/src/Codeception/Module/PhpBrowser.php) or [`WebDriver`](https://github.com/Codeception/Codeception/blob/2.2/src/Codeception/Module/WebDriver.php) modules enabled.

Nevertheless the module is fully-configurable. It consist of three major components: [`provider`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/MarkupProviderInterface.php) with provides markup to validate, [`validator`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/MarkupValidatorInterface.php) which performs actual markup validation and [`reporter`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/MarkupReporterInterface.php) which reports about validation messages. You may configure each of the components with a custom implementation.

### Provider
The module may be configured with a custom `provider` which will provide the markup to the `validator`. The [`default provider`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/DefaultMarkupProvider.php) tries to obtain markup from the `PhpBrowser` and `WebDriver` modules.

### Validator
The module may be configured with a custom `validator` which will validate markup received from the `provider`. The [default validator](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/W3CMarkupValidator.php) uses the [W3C Markup Validation Service API](https://validator.w3.org/docs/api.html).

### Reporter
The module may be configured with a custom `reporter` which will report about messages received from the `validator`. You may implement you own reporter or tweak a [default one](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/DefaultMarkupReporter.php).
```yaml
class_name: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: 'http://localhost/'
        - Kolyunya\Codeception\Module\MarkupValidator:
            reporter:
                class: Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter
                config:
                    ignoreWarnings: true
                    ignoredErrors:
                        - '/some error/'
                        - '/another error/'
```
