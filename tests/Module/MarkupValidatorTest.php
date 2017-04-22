<?php

namespace Kolyunya\Codeception\Tests\Module;

use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use Codeception\Lib\ModuleContainer;
use PHPUnit\Framework\TestCase;
use Kolyunya\Codeception\Module\MarkupValidator;

class MarkupValidatorTest extends TestCase
{
    /**
     * @var ModuleContainer|PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleContainer;

    /**
     * @var MarkupValidator|PHPUnit_Framework_MockObject_MockObject
     */
    private $module;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->moduleContainer = $this
            ->getMockBuilder('Codeception\Lib\ModuleContainer')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'hasModule',
                'getModule',
            ))
            ->getMock()
        ;

        $this->module = $this
            ->getMockBuilder('Kolyunya\Codeception\Module\MarkupValidator')
            ->setConstructorArgs(array(
                $this->moduleContainer,
                null,
            ))
            ->enableProxyingToOriginalMethods()
            ->getMock()
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }

    public function testInvalidProvider()
    {
        $this->setExpectedException('Exception', 'Invalid class «stdClass» provided for component «provider».');

        $this->module = new MarkupValidator($this->moduleContainer, array(
            'provider' => array(
                'class' => 'stdClass',
            ),
        ));
    }

    public function testInvalidValidator()
    {
        $this->setExpectedException('Exception', 'Invalid class «stdClass» provided for component «validator».');

        $this->module = new MarkupValidator($this->moduleContainer, array(
            'validator' => array(
                'class' => 'stdClass',
            ),
        ));
    }

    public function testInvalidReporter()
    {
        $this->setExpectedException('Exception', 'Invalid class «stdClass» provided for component «reporter».');

        $this->module = new MarkupValidator($this->moduleContainer, array(
            'reporter' => array(
                'class' => 'stdClass',
            ),
        ));
    }

    public function testInvalidComponentClass()
    {
        $this->setExpectedException('Exception', 'Invalid class configuration of component «reporter».');

        $this->module = new MarkupValidator($this->moduleContainer, array(
            'reporter' => array(
                'class' => false,
            ),
        ));
    }

    public function testInvalidComponentConfig()
    {
        $this->setExpectedException('Exception', 'Invalid configuration of component «reporter».');

        $this->module = new MarkupValidator($this->moduleContainer, array(
            'reporter' => array(
                'class' => 'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter',
                'config' => 'configuration-parameter',
            ),
        ));
    }

    /**
     * @dataProvider testValidateMarkupDataProvider
     */
    public function testValidateMarkup($markup, $valid)
    {
        $this->mockMarkup($markup);

        if ($valid === true) {
            $this->module->validateMarkup($markup);
            $this->assertTrue(true);
            return;
        }

        try {
            $this->module->validateMarkup($markup);
        } catch (Exception $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->assertTrue(false);
    }

    public function testValidateMarkupDataProvider()
    {
        return array(
            array(
                <<<HTML
                    <!DOCTYPE HTML>
                    <html>
                        <head>
                            <title>
                                A valid page.
                            </title>
                        </head>
                    </html>
HTML
                ,
                true,
            ),
            array(
                <<<HTML
                    <!DOCTYPE HTML>
                    <html>
                        <head>
                        </head>
                    </html>
HTML
                ,
                false,
            ),
            array(
                <<<HTML
                    <!DOCTYPE HTML>
                    <html>
                        <head>
                        </head>
                        <body>
                            <form>
                                <button role="button">
                                </button>
                            </form>
                        </body>
                    </html>
HTML
                ,
                false,
            ),
            array(
                <<<HTML
                    <!DOCTYPE HTML>
                    <html>
                        <head>
                            <title>
                                A page with a warning.
                            </title>
                        </head>
                        <body>
                            <form>
                                <button role="button">
                                </button>
                            </form>
                        </body>
                    </html>
HTML
                ,
                false,
            ),
        );
    }

    private function mockMarkup($markup)
    {
        $phpBrowser = $this
            ->getMockBuilder('Codeception\Module\PhpBrowser')
            ->disableOriginalConstructor()
            ->setMethods(array(
                '_getResponseContent',
            ))
            ->getMock()
        ;
        $phpBrowser
            ->method('_getResponseContent')
            ->will($this->returnValue($markup))
        ;

        $this->moduleContainer
            ->method('hasModule')
            ->will($this->returnValueMap(array(
                array('PhpBrowser', true),
            )))
        ;
        $this->moduleContainer
            ->method('getModule')
            ->will($this->returnValueMap(array(
                array('PhpBrowser', $phpBrowser),
            )))
        ;
    }
}
