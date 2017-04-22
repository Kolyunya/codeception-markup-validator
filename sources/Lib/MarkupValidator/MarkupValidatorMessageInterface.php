<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

/**
 * An interface of a markup validator message.
 */
interface MarkupValidatorMessageInterface
{
    /**
     * An undefined message.
     */
    const TYPE_UNDEFINED = 'undefined';

    /**
     * An informational message.
     */
    const TYPE_INFO = 'info';

    /**
     * A warning message.
     */
    const TYPE_WARNING = 'warning';

    /**
     * An error message.
     */
    const TYPE_ERROR = 'error';

    /**
     * Returns a string representation of a message.
     *
     * @return string A string representation of a message.
     */
    public function __toString();

    /**
     * Returns message type.
     *
     * @return string Message type.
     */
    public function getType();

    /**
     * Returns message summary.
     *
     * @return string|null Message summary.
     */
    public function getSummary();

    /**
     * Returns message details.
     *
     * @return string|null Message details.
     */
    public function getDetails();

    /**
     * Returns related markup.
     *
     * @return string|null Related markup.
     */
    public function getMarkup();
}
