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

If you need, you may override module-wide message filter configuration for each page individually like this:
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

// Set error count threshold, do not ignore warnings
// but ignore some errors on this page.
$I->amOnPage('/quux/');
$I->validateMarkup(array(
    'errorCountThreshold' => 10,
    'ignoreWarnings' => false,
    'ignoredErros' => array(
        '/this error/',
        '/that error/',
    ),
));
```

## Configuration
The module does not require any configuration. The default setup will work if you have either [`PhpBrowser`](https://github.com/Codeception/Codeception/blob/2.2/src/Codeception/Module/PhpBrowser.php) or [`WebDriver`](https://github.com/Codeception/Codeception/blob/2.2/src/Codeception/Module/WebDriver.php) modules enabled.

Nevertheless the module is fully-configurable and hackable. It consist of four major components: [`provider`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/MarkupProviderInterface.php) with provides markup to validate, [`validator`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/MarkupValidatorInterface.php) which performs actual markup validation, [`filter`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/MessageFilterInterface.php) which filters messages received from the validator and  [`printer`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/MessagePrinterInterface.php) which prints messages received from the validator. You may configure each of the components with a custom implementation.

### Provider
The module may be configured with a custom `provider` which will provide the markup to the `validator`. The [`default provider`](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/DefaultMarkupProvider.php) tries to obtain markup from the `PhpBrowser` and `WebDriver` modules.
```yaml
class_name: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: 'http://localhost/'
        - Kolyunya\Codeception\Module\MarkupValidator:
            provider:
                class: Acme\Tests\CustomMarkupProvider
```

### Validator
The module may be configured with a custom `validator` which will validate markup received from the `provider`. The [default validator](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/W3CMarkupValidator.php) uses the [W3C Markup Validation Service API](https://validator.w3.org/docs/api.html).
```yaml
class_name: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: 'http://localhost/'
        - Kolyunya\Codeception\Module\MarkupValidator:
            validator:
                class: Acme\Tests\CustomMarkupValidator
```

### Filter
The module may be configured with a custom `filter` which will report about messages received from the `validator`. You may implement you own filter or tweak a [default one](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/DefaultMessageFilter.php).
```yaml
class_name: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: 'http://localhost/'
        - Kolyunya\Codeception\Module\MarkupValidator:
            filter:
                class: Kolyunya\Codeception\Lib\MarkupValidator\DefaultMessageFilter
                config:
                    errorCountThreshold: 10
                    ignoreWarnings: true
                    ignoredErrors:
                        - '/some error/'
                        - '/another error/'
```

### Printer
The module may be configured with a custom `printer` which defines how messages received from the validator are printed. The [default printer](https://github.com/Kolyunya/codeception-markup-validator/blob/master/sources/Lib/MarkupValidator/DefaultMessagePrinter.php) prints message type, summary, details, first line number, last line number and related markup.
```yaml
class_name: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: 'http://localhost/'
        - Kolyunya\Codeception\Module\MarkupValidator:
            printer:
                class: Acme\Tests\CustomMessagePrinter
```
