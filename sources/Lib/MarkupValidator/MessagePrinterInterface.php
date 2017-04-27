<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

/**
 * An interface of a markup validator message printer.
 */
interface MessagePrinterInterface
{
    /**
     * Returns a string representation of a single message.
     *
     * @return string A string representation of a single message.
     */
    public function getMessageString(MarkupValidatorMessageInterface $message);

    /**
     * Returns a string representation of multiple message.
     *
     * @return MarkupValidatorMessageInterface[] A string representation of multiple message.
     */
    public function getMessagesString(array $messages);
}
