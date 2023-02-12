<?php

namespace Kolyunya\Codeception\Tests\Lib\MarkupValidator;

use Codeception\Lib\ModuleContainer;
use Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupProvider;
use Kolyunya\Codeception\Tests\Base\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class DefaultMarkupProviderTest extends TestCase
{
    /**
     * @var ModuleContainer|PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleContainer;

    /**
     * @var DefaultMarkupProvider
     */
    private $provider;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $this->moduleContainer = $this
            ->getMockBuilder('Codeception\Lib\ModuleContainer')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->provider = new DefaultMarkupProvider($this->moduleContainer);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
    }

    public function testWithNoPhpBrowserNoWebDriver()
    {
        $this->setExpectedException('Exception', 'Unable to obtain current page markup.');
        $this->provider->getMarkup();
    }

    public function testWithPhpBrowser()
    {
        $expectedMarkup =
            <<<HTML
                <!DOCTYPE HTML>
                <html lang="en">
                    <head>
                        <title>
                            A valid page.
                        </title>
                    </head>
                </html>
HTML
        ;

        $phpBrowser = $this
            ->getMockBuilder('Codeception\Module')
            ->disableOriginalConstructor()
            ->addMethods(array(
                '_getResponseContent',
            ))
            ->getMock()
        ;
        $phpBrowser
            ->method('_getResponseContent')
            ->will($this->returnValue($expectedMarkup))
        ;

        $this->moduleContainer
            ->method('hasModule')
            ->will($this->returnValueMap(array(
                array('PhpBrowser', true)
            )))
        ;
        $this->moduleContainer
            ->method('getModule')
            ->will($this->returnValueMap(array(
                array('PhpBrowser', $phpBrowser)
            )))
        ;

        $actualMarkup = $this->provider->getMarkup();
        $this->assertEquals($expectedMarkup, $actualMarkup);
    }

    public function testWithWebDriver()
    {
        $expectedMarkup =
            <<<HTML
                <!DOCTYPE HTML>
                <html lang="en">
                    <head>
                        <title>
                            A valid page.
                        </title>
                    </head>
                </html>
HTML
        ;

        $remoteWebDriver = $this
            ->getMockBuilder('Codeception\Module')
            ->disableOriginalConstructor()
            ->addMethods(array(
                'getPageSource',
            ))
            ->getMock()
        ;
        $remoteWebDriver
            ->method('getPageSource')
            ->will($this->returnValue($expectedMarkup))
        ;

        $webDriver = $this
            ->getMockBuilder('Codeception\Module')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $webDriver->webDriver = $remoteWebDriver;

        $this->moduleContainer
            ->method('hasModule')
            ->will($this->returnValueMap(array(
                array('PhpBrowser', false),
                array('WebDriver', true)
            )))
        ;
        $this->moduleContainer
            ->method('getModule')
            ->will($this->returnValueMap(array(
                array('WebDriver', $webDriver)
            )))
        ;

        $actualMarkup = $this->provider->getMarkup();
        $this->assertEquals($expectedMarkup, $actualMarkup);
    }
}
