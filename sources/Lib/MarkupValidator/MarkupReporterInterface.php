<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

/**
 * An interface of a markup reported.
 */
interface MarkupReporterInterface
{
    /**
     * Constructs a markup reported. Injects configuration parameters.
     *
     * @param array $config Configuration parameters.
     */
    public function __construct(array $config);

    /**
     * Reports about markup validation messages.
     *
     * @param MarkupValidatorMessageInterface $message Message to report about.
     */
    public function report(MarkupValidatorMessageInterface $message);
}
