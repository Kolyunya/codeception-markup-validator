<?php

namespace Kolyunya\Codeception\Tests\Lib\Base;

use PHPUnit\Framework\TestCase;
use Kolyunya\Codeception\Lib\Base\Component;
use Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupProvider;
use Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter;
use Kolyunya\Codeception\Lib\MarkupValidator\W3CMarkupValidator;

class ComponentTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }

    /**
     * @dataProvider testGetClassNameDataProvider
     */
    public function testGetClassName($classNameActual, $classNameExpected)
    {
        $this->assertEquals($classNameActual, $classNameExpected);
    }

    public function testGetClassNameDataProvider()
    {
        return array(
            array(
                'Kolyunya\Codeception\Lib\Base\Component',
                Component::getClassName(),
            ),
            array(
                'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupProvider',
                DefaultMarkupProvider::getClassName(),
            ),
            array(
                'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter',
                DefaultMarkupReporter::getClassName(),
            ),
            array(
                'Kolyunya\Codeception\Lib\MarkupValidator\W3CMarkupValidator',
                W3CMarkupValidator::getClassName(),
            ),
        );
    }
}
