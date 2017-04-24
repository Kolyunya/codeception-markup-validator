# Codeception Markup Validator
[![Latest Stable Version](https://poser.pugx.org/kolyunya/codeception-markup-validator/v/stable)](https://packagist.org/packages/kolyunya/codeception-markup-validator)
[![Build Status](https://travis-ci.org/Kolyunya/codeception-markup-validator.svg?branch=master)](https://travis-ci.org/Kolyunya/codeception-markup-validator)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![Coverage Status](https://img.shields.io/coveralls/Kolyunya/codeception-markup-validator/master.svg)](https://coveralls.io/github/Kolyunya/codeception-markup-validator?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Kolyunya/codeception-markup-validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Kolyunya/codeception-markup-validator/?branch=master)
[![Code Climate](https://codeclimate.com/github/Kolyunya/codeception-markup-validator/badges/gpa.svg)](https://codeclimate.com/github/Kolyunya/codeception-markup-validator)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c7566433-052d-41f1-ab34-857f4650a18a/mini.png)](https://insight.sensiolabs.com/projects/c7566433-052d-41f1-ab34-857f4650a18a)

## Description
Markup validator module for Codeception. Validates web-page markup (HTML, XHTML etc.) via markup validators e.g. [W3C Markup Validator Service](https://validator.w3.org/docs/api.html). Zero effort tests which will inform you when your markup gets broken.
```php
$I->amOnPage('/');
$I->validateMarkup();
```

Dead simple to use. Requires literally no configuraton. Works as you expect it out of box. Fully configurable and extendable if you want to hack it. Each component of the module can be replaced with a custom implementation. Just implement a simple interface and inject your custom component to the module.

## Installation
The recommended way of module installation is via composer:
```sh
composer require --dev kolyunya/codeception-markup-validator
```

## Usage
Add the module to your acceptance test suit configuration:
```yaml
class_name: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: 'http://localhost/'
        - Kolyunya\Codeception\Module\MarkupValidator
```

Build the test suit:
```sh
codecept build
```

Validate markup:
```php
$I->amOnPage('/');
$I->validateMarkup();
```

If you need, you may override the module-wide message reporter configuration for each page individually like this:
```php
// Perform very strict checks for this particular page.
$I->amOnPage('/foo/');
$I->validateMarkup(array(
    'ignoreWarnings' => false,
));

// Ignore those two errors just on this page.
$I->amOnPage('/bar/');
$I->validateMarkup(array(
    'ignoredErrors' => array(
        '/some error/',
        '/another error/',
    ),
));

// Ignore all errors just on this page.
$I->amOnPage('/baz/');
$I->validateMarkup(array(
    'ignoredErrors' => array(
        '/.*/',
    ),
));
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
