<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Codeception\Util\Shared\Asserts;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupReporterInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

/**
 * Default markup validation message reporter.
 */
class DefaultMarkupReporter implements MarkupReporterInterface
{
    /**
     * Use asserts to report messages.
     */
    use Asserts;

    /**
     * Configuration parameters.
     *
     *  array(
     *      'ignoreWarnings' => false,
     *      'ignoredErrors' => array(),
     *  )
     *
     * @var array
     */
    private $config;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function report(MarkupValidatorMessageInterface $message)
    {
        $messageType = $message->getType();

        if ($messageType === MarkupValidatorMessageInterface::TYPE_UNDEFINED ||
            $messageType === MarkupValidatorMessageInterface::TYPE_INFO
        ) {
            return;
        }

        if ($messageType === MarkupValidatorMessageInterface::TYPE_WARNING &&
            $this->ignoreWarnings() === true
        ) {
            return;
        }

        if ($this->ignoreError($message->getSummary()) === true) {
            return;
        }

        $this->reportError(
            $message->getSummary(),
            $message->getDetails()
        );
    }

    /**
     * Reports a markup validation error.
     *
     * @param string $summary Markup validation error summary.
     * @param string $details Markup validation error details.
     */
    private function reportError($summary, $details)
    {
        $template = 'Markup validation error. %s. Details: %s';
        $message = sprintf($template, $summary, $details);
        $this->fail($message);
    }

    /**
     * Returns a boolean indicating whether the reporter ignores warnings or not.
     *
     * @return bool Whether the reporter ignores warnings or not.
     */
    private function ignoreWarnings()
    {
        $ignoreWarnings = false;

        $ignoreWarningsConfigKey = 'ignoreWarnings';
        if (isset($this->config[$ignoreWarningsConfigKey]) === true) {
            $ignoreWarnings = $this->config[$ignoreWarningsConfigKey];
        }

        return $ignoreWarnings;
    }

    /**
     * Returns a boolean indicating whether an error is ignored or not.
     *
     * @param string $summary Error summary.
     * @return boolean Whether an error is ignored or not.
     */
    private function ignoreError($summary)
    {
        $ignoreError = false;

        $ignoredErrorsConfigKey = 'ignoredErrors';
        if (isset($this->config[$ignoredErrorsConfigKey]) === true &&
            is_array($this->config[$ignoredErrorsConfigKey]) === true
        ) {
            $ignoredErrors = $this->config[$ignoredErrorsConfigKey];
            foreach ($ignoredErrors as $ignoredError) {
                $erorIsIgnored = preg_match($ignoredError, $summary) === 1;
                if ($erorIsIgnored) {
                    $ignoreError = true;
                    break;
                }
            }
        }

        return $ignoreError;
    }
}
