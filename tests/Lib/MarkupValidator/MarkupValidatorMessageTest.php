<?php

namespace Kolyunya\Codeception\Tests\Lib\MarkupValidator;

use PHPUnit\Framework\TestCase;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessage;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

class MarkupValidatorMessageTest extends TestCase
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

    public function testDefaultInitialization()
    {
        $message = new MarkupValidatorMessage();

        $this->assertEquals(
            $message->getType(),
            MarkupValidatorMessageInterface::TYPE_UNDEFINED
        );
        $this->assertNull($message->getSummary());
        $this->assertNull($message->getDetails());
        $this->assertNull($message->getMarkup());
    }

    /**
     * @dataProvider testCustomInitializationDataProvider
     */
    public function testCustomInitialization($type, $summary, $details, $markup)
    {
        $message = new MarkupValidatorMessage($type, $summary, $details, $markup);

        $this->assertEquals($message->getType(), $type);
        $this->assertEquals($message->getSummary(), $summary);
        $this->assertEquals($message->getDetails(), $details);
        $this->assertEquals($message->getMarkup(), $markup);
    }

    /**
     * @dataProvider testToStringDataProvider
     */
    public function testToString($type, $summary, $details, $markup, $string)
    {
        $message = new MarkupValidatorMessage($type, $summary, $details, $markup);
        $messageString = $message->__toString();
        $this->assertEquals($string, $messageString);
    }

    public function testCustomInitializationDataProvider()
    {
        return array(
            array(
                'type' => null,
                'summary' => null,
                'details' => null,
                'markup' => null,
            ),
            array(
                'type' => MarkupValidatorMessageInterface::TYPE_UNDEFINED,
                'summary' => null,
                'details' => null,
                'markup' => null,
            ),
            array(
                'type' => MarkupValidatorMessageInterface::TYPE_ERROR,
                'summary' => 'Short error summary.',
                'details' => 'Detailed error description.',
                'markup' => '<html></html>',
            ),
        );
    }

    public function testToStringDataProvider()
    {
        return array(
            array(
                'type' => null,
                'summary' => null,
                'details' => null,
                'markup' => null,
                <<<TXT
Markup validator message:
Type: unavailable
Summary: unavailable
Details: unavailable
Markup: unavailable

TXT
                ,
            ),
            array(
                'type' => MarkupValidatorMessageInterface::TYPE_UNDEFINED,
                'summary' => null,
                'details' => null,
                'markup' => null,
                <<<TXT
Markup validator message:
Type: undefined
Summary: unavailable
Details: unavailable
Markup: unavailable

TXT
                ,
            ),
            array(
                'type' => MarkupValidatorMessageInterface::TYPE_ERROR,
                'summary' => 'Short error summary.',
                'details' => 'Detailed error description.',
                'markup' => '<html></html>',
                <<<TXT
Markup validator message:
Type: error
Summary: Short error summary.
Details: Detailed error description.
Markup: <html></html>

TXT
                ,
            ),
        );
    }
}
