<?php

namespace Kolyunya\Codeception\Tests\Lib\MarkupValidator;

use Exception;
use PHPUnit\Framework\TestCase;
use Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessage;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

class DefaultMarkupReporterTest extends TestCase
{
    /**
     * @var DefaultMarkupReporter
     */
    private $reporter;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->reporter = new DefaultMarkupReporter();
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
    public function testMessageNotReported($messages)
    {
        $this->reporter->report($messages);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider testMessageReportedDataProvider
     */
    public function testMessageReported($messages, $report)
    {
        $this->reporter->setConfiguration(array(
            'ignoreWarnings' => false,
            'ignoredErrors' => array(),
        ));

        try {
            $this->reporter->report($messages);
        } catch (Exception $exception) {
            $exceptionMessage = $exception->getMessage();
            $this->assertTrue(is_string($exceptionMessage));
            $this->assertContains($report, $exceptionMessage);
            return;
        }

        $this->fail('Message was not reported.');
    }

    /**
     * @dataProvider testerrorCountThresholdDataProvider
     */
    public function testerrorCountThreshold($messages, $threshold)
    {
        $this->reporter->setConfiguration(array(
            'errorCountThreshold' => $threshold,
        ));

        $this->reporter->report($messages);
        $this->assertTrue(true);
    }

    public function testIgnoreWarnings()
    {
        $this->reporter->setConfiguration(array(
            'ignoreWarnings' => true,
        ));

        $warning = new MarkupValidatorMessage();
        $warning->setType(MarkupValidatorMessageInterface::TYPE_WARNING);

        $this->reporter->report(array($warning));
        $this->assertTrue(true);
    }

    /**
     * @dataProvider testIgnoredErrorsDataProvider
     */
    public function testIgnoredErrors($messages, $ignore, $isIgnored)
    {
        $this->reporter->setConfiguration(array(
            'ignoredErrors' => array(
                $ignore,
            ),
        ));

        if ($isIgnored === true) {
            $this->reporter->report($messages);
            $this->assertTrue(true);
            return;
        }

        try {
            $this->reporter->report($messages);
        } catch (Exception $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail();
    }

    public function testInvaliderrorCountThresholdConfig()
    {
        $this->setExpectedException('Exception', 'Invalid «errorCountThreshold» config key.');

        $warning = new MarkupValidatorMessage();
        $warning->setType(MarkupValidatorMessageInterface::TYPE_WARNING);

        $this->reporter->setConfiguration(array(
            'errorCountThreshold' => true,
        ));
        $this->reporter->report(array($warning));
    }

    public function testInvalidIgnoreWarningsConfig()
    {
        $this->setExpectedException('Exception', 'Invalid «ignoreWarnings» config key.');

        $warning = new MarkupValidatorMessage();
        $warning->setType(MarkupValidatorMessageInterface::TYPE_WARNING);

        $this->reporter->setConfiguration(array(
            'ignoreWarnings' => array(
                'foo' => false,
                'bar' => true,
            ),
        ));
        $this->reporter->report(array($warning));
    }

    public function testInvalidIgnoreErrorsConfig()
    {
        $this->setExpectedException('Exception', 'Invalid «ignoredErrors» config key.');

        $error = new MarkupValidatorMessage();
        $error->setType(MarkupValidatorMessageInterface::TYPE_ERROR);

        $this->reporter->setConfiguration(array(
            'ignoredErrors' => false,
        ));
        $this->reporter->report(array($error));
    }

    public function testerrorCountThresholdDataProvider()
    {
        return array(
            array(
                array(
                ),
                0,
            ),
            array(
                array(
                    new MarkupValidatorMessage(MarkupValidatorMessageInterface::TYPE_ERROR),
                ),
                1,
            ),
            array(
                array(
                    new MarkupValidatorMessage(MarkupValidatorMessageInterface::TYPE_ERROR),
                    new MarkupValidatorMessage(MarkupValidatorMessageInterface::TYPE_ERROR),
                ),
                2,
            ),
            array(
                array(
                    new MarkupValidatorMessage(MarkupValidatorMessageInterface::TYPE_ERROR),
                    new MarkupValidatorMessage(MarkupValidatorMessageInterface::TYPE_ERROR),
                    new MarkupValidatorMessage(MarkupValidatorMessageInterface::TYPE_ERROR),
                ),
                5,
            ),
        );
    }

    public function testMessageNotReportedDataProvider()
    {
        return array(
            array(
                array(
                    (new MarkupValidatorMessage())
                        ->setType(MarkupValidatorMessageInterface::TYPE_UNDEFINED)
                ),
            ),
            array(
                array(
                    (new MarkupValidatorMessage())
                        ->setType(MarkupValidatorMessageInterface::TYPE_INFO)
                ),
            ),
        );
    }

    public function testMessageReportedDataProvider()
    {
        return array(
            array(
                array(
                    (new MarkupValidatorMessage())
                        ->setType(MarkupValidatorMessageInterface::TYPE_WARNING)
                        ->setSummary('Warning text.')
                        ->setMarkup('<h1></h1>')
                    ,
                ),
                'Warning text.',
            ),
            array(
                array(
                    (new MarkupValidatorMessage())
                        ->setType(MarkupValidatorMessageInterface::TYPE_ERROR)
                        ->setSummary('Error text.')
                        ->setMarkup('<title></title>')
                    ,
                ),
                'Error text.',
            ),
        );
    }

    public function testIgnoredErrorsDataProvider()
    {
        return array(
            array(
                array(
                    (new MarkupValidatorMessage())
                        ->setType(MarkupValidatorMessageInterface::TYPE_ERROR)
                        ->setSummary('Some cryptic error message.')
                    ,
                ),
                '/cryptic error/',
                true,
            ),
            array(
                array(
                    (new MarkupValidatorMessage())
                        ->setType(MarkupValidatorMessageInterface::TYPE_ERROR)
                        ->setSummary('Case insensitive error message.')
                    ,
                ),
                '/case insensitive error message./i',
                true,
            ),
            array(
                array(
                    (new MarkupValidatorMessage())
                        ->setType(MarkupValidatorMessageInterface::TYPE_ERROR)
                        ->setSummary('Текст ошибки в UTF-8.')
                    ,
                ),
                '/Текст ошибки в UTF-8./u',
                true,
            ),
            array(
                array(
                    (new MarkupValidatorMessage())
                        ->setType(MarkupValidatorMessageInterface::TYPE_ERROR)
                    ,
                ),
                '/error/',
                false,
            ),
        );
    }
}
