<?php

namespace Kolyunya\Codeception\Tests\Module;

use Exception;
use Codeception\Lib\ModuleContainer;
use PHPUnit\Framework\TestCase;
use Kolyunya\Codeception\Module\MarkupValidator;

class MarkupValidatorTest extends TestCase
{
    /**
     * @var ModuleContainer
     */
    private $moduleContainer;

    /**
     * @var MarkupValidator
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

    /**
     * @dataProvider testValidateMarkupDataProvider
     */
    public function testValidateMarkup($markup, $valid)
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
        );
    }
}
