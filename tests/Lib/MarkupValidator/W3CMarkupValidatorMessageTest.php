<?php

namespace Kolyunya\Codeception\Tests\Lib\MarkupValidator;

use PHPUnit\Framework\TestCase;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\W3CMarkupValidatorMessage;

class W3CMarkupValidatorMessageTest extends TestCase
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
     * @dataProvider testConstructorDataProvider
     */
    public function testConstructor($data, $type, $summary, $details, $markup)
    {
        $message = new W3CMarkupValidatorMessage($data);

        $this->assertEquals($message->getType(), $type);
        $this->assertEquals($message->getSummary(), $summary);
        $this->assertEquals($message->getDetails(), $details);
        $this->assertEquals($message->getMarkup(), $markup);
    }

    public function testConstructorDataProvider()
    {
        return array(
            array(
                'data' => array(
                    'type' => 'error',
                    'lastLine' => 4,
                    'lastColumn' => 27,
                    'firstColumn' => 21,
                    'message' => 'Element “head” is missing a required instance of child element “title”.',
                    'extract' => '</head>\n',
                    'hiliteStart' => 10,
                    'hiliteLength' => 6,
                ),
                'type' => MarkupValidatorMessageInterface::TYPE_ERROR,
                'summary' => 'Element “head” is missing a required instance of child element “title”.',
                'details' => null,
                'markup' => '</head>\n',
            ),
            array(
                'data' => array(
                    'type' => 'info',
                    'lastLine' => 7,
                    'lastColumn' => 50,
                    'firstColumn' => 29,
                    'subType' => 'warning',
                    'message' => 'The “button” role is unnecessary for element “button”.',
                    'extract' => ' <button role=\"button\">\n ',
                    'hiliteStart' => 10,
                    'hiliteLength' => 22,
                ),
                'type' => MarkupValidatorMessageInterface::TYPE_WARNING,
                'summary' => 'The “button” role is unnecessary for element “button”.',
                'details' => null,
                'markup' => ' <button role=\"button\">\n ',
            ),
            array(
                'data' => array(
                    'type' => 'info',
                    'lastLine' => 7,
                    'lastColumn' => 50,
                    'firstColumn' => 29,
                    'subType' => null,
                    'message' => 'Informative message.',
                    'extract' => null,
                    'hiliteStart' => 10,
                    'hiliteLength' => 22,
                ),
                'type' => MarkupValidatorMessageInterface::TYPE_INFO,
                'summary' => 'Informative message.',
                'details' => null,
                'markup' => null,
            ),
            array(
                'data' => array(
                    'type' => 'info',
                    'lastLine' => 7,
                    'lastColumn' => 50,
                    'firstColumn' => 29,
                    'subType' => 'undefined',
                    'message' => 'Informative message.',
                    'extract' => '<some-markup></some-markup>',
                    'hiliteStart' => 10,
                    'hiliteLength' => 22,
                ),
                'type' => MarkupValidatorMessageInterface::TYPE_INFO,
                'summary' => 'Informative message.',
                'details' => null,
                'markup' => '<some-markup></some-markup>',
            ),
        );
    }
}
