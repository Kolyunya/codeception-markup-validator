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
    const IGNORE_WARNINGS_CONFIG_KEY = 'ignoreWarnings';

    const IGNORED_ERRORS_CONFIG_KEY = 'ignoredErrors';

    /**
     * Use asserts to report messages.
     */
    use Asserts;

    /**
     * Configuration parameters.
     *
     * @var array
     */
    private $config = array(
        self::IGNORE_WARNINGS_CONFIG_KEY => true,
        self::IGNORED_ERRORS_CONFIG_KEY => array(),
    );

    /**
     * {@inheritDoc}
     */
    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->config, $config);
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

        $messageString = $message->__toString();
        $this->fail($messageString);
    }

    /**
     * Returns a boolean indicating whether the reporter ignores warnings or not.
     *
     * @return bool Whether the reporter ignores warnings or not.
     */
    private function ignoreWarnings()
    {
        $ignoreWarnings = false;

        if (isset($this->config[self::IGNORE_WARNINGS_CONFIG_KEY]) === true &&
            is_bool($this->config[self::IGNORE_WARNINGS_CONFIG_KEY]) === true
        ) {
            /* @var $ignoreWarnings bool */
            $ignoreWarnings = $this->config[self::IGNORE_WARNINGS_CONFIG_KEY];
        }

        return $ignoreWarnings;
    }

    /**
     * Returns a boolean indicating whether an error is ignored or not.
     *
     * @param string|null $summary Error summary.
     * @return boolean Whether an error is ignored or not.
     */
    private function ignoreError($summary)
    {
        $ignoreError = false;

        if ($summary === null) {
            return $ignoreError;
        }

        if (isset($this->config[self::IGNORED_ERRORS_CONFIG_KEY]) === true &&
            is_array($this->config[self::IGNORED_ERRORS_CONFIG_KEY]) === true
        ) {
            $ignoredErrors = $this->config[self::IGNORED_ERRORS_CONFIG_KEY];
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
