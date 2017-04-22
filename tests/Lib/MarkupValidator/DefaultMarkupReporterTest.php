<?php

namespace Kolyunya\Codeception\Tests\Lib\MarkupValidator;

use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit\Framework\TestCase;
use Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessage;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

class DefaultMarkupReporterTest extends TestCase
{
    /**
     * @var DefaultMarkupReporter|PHPUnit_Framework_MockObject_MockObject
     */
    private $markupReporter;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->markupReporter = $this
            ->getMockBuilder('Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter')
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
     * @dataProvider testMessageNotReportedDataProvider
     */
    public function testMessageNotReported(MarkupValidatorMessageInterface $message)
    {
        $this->markupReporter->report($message);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider testMessageReportedDataProvider
     */
    public function testMessageReported(MarkupValidatorMessageInterface $message, $report)
    {
        try {
            $this->markupReporter->report($message);
        } catch (Exception $exception) {
            $exceptionMessage = $exception->getMessage();
            $this->assertTrue(is_string($exceptionMessage));
            $this->assertContains($report, $exceptionMessage);
            return;
        }

        $this->fail('Message was not reported.');
    }

    public function testIgnoreWarnings()
    {
        $this->markupReporter = new DefaultMarkupReporter(array(
            'ignoreWarnings' => true,
        ));

        $warning = new MarkupValidatorMessage(
            MarkupValidatorMessageInterface::TYPE_WARNING
        );

        $this->markupReporter->report($warning);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider testIgnoredErrorsDataProvider
     */
    public function testIgnoredErrors($message, $ignore, $isIgnored)
    {
        $this->markupReporter = new DefaultMarkupReporter(array(
            'ignoredErrors' => array(
                $ignore,
            ),
        ));

        if ($isIgnored === true) {
            $this->markupReporter->report($message);
            $this->assertTrue(true);
            return;
        }

        try {
            $this->markupReporter->report($message);
        } catch (Exception $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail();
    }

    public function testMessageNotReportedDataProvider()
    {
        return array(
            array(
                new MarkupValidatorMessage(
                    MarkupValidatorMessageInterface::TYPE_UNDEFINED
                ),
            ),
            array(
                new MarkupValidatorMessage(
                    MarkupValidatorMessageInterface::TYPE_INFO
                ),
            ),
        );
    }

    public function testMessageReportedDataProvider()
    {
        return array(
            array(
                new MarkupValidatorMessage(
                    MarkupValidatorMessageInterface::TYPE_WARNING,
                    'Warning text.',
                    null,
                    '<h1></h1>'
                ),
                'Warning text.',
            ),
            array(
                new MarkupValidatorMessage(
                    MarkupValidatorMessageInterface::TYPE_ERROR,
                    'Error text.',
                    null,
                    '<title></title>'
                ),
                'Error text.',
            ),
        );
    }

    public function testIgnoredErrorsDataProvider()
    {
        return array(
            array(
                new MarkupValidatorMessage(
                    MarkupValidatorMessageInterface::TYPE_ERROR,
                    'Some cryptic error message.'
                ),
                '/cryptic error/',
                true,
            ),
            array(
                new MarkupValidatorMessage(
                    MarkupValidatorMessageInterface::TYPE_ERROR,
                    'Case insensitive error message.'
                ),
                '/case insensitive error message./i',
                true,
            ),
            array(
                new MarkupValidatorMessage(
                    MarkupValidatorMessageInterface::TYPE_ERROR,
                    'Текст ошибки в UTF-8.'
                ),
                '/Текст ошибки в UTF-8./u',
                true,
            ),
            array(
                new MarkupValidatorMessage(
                    MarkupValidatorMessageInterface::TYPE_ERROR
                ),
                '/error/',
                false,
            ),
        );
    }
}
