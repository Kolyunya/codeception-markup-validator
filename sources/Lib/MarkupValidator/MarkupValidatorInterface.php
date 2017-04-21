<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

/**
 * An interface of a markup validator.
 */
interface MarkupValidatorInterface
{
    /**
     * Constructs a markup provider. Injects configuration parameters.
     *
     * @param array $config Configuration parameters.
     */
    public function __construct(array $config);

    /**
     * Validates markup and returns validation messages.
     *
     * @param string $markup Markup to validate.
     *
     * @return MarkupValidatorMessageInterface[] An array of messages.
     */
    public function validate($markup);
}
