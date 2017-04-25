<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Kolyunya\Codeception\Lib\Base\ComponentInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

/**
 * An interface of a markup reported.
 */
interface MarkupReporterInterface extends ComponentInterface
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
     * @param MarkupValidatorMessageInterface[] $messages All message to report about.
     */
    public function report(array $messages);
}
