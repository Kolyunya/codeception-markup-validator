<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Exception;
use Codeception\Util\Shared\Asserts;
use Kolyunya\Codeception\Lib\Base\Component;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupReporterInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

/**
 * Default markup validation message reporter.
 */
class DefaultMarkupReporter extends Component implements MarkupReporterInterface
{
    const ERROR_COUNT_THRESHOLD_KEY = 'errorCountThreshold';

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
    protected $configuration = array(
        self::ERROR_COUNT_THRESHOLD_KEY => 0,
        self::IGNORE_WARNINGS_CONFIG_KEY => true,
        self::IGNORED_ERRORS_CONFIG_KEY => array(),
    );

    /**
     * {@inheritDoc}
     */
    public function __construct(array $configuration = array())
    {
        parent::__construct($configuration);
    }

    /**
     * {@inheritDoc}
     */
    public function report(array $messages)
    {
        $filteredMessages = $this->filterMesages($messages);

        if ($this->belowErrorCountThreshold($filteredMessages) === true) {
            return;
        }

        $report = implode("\n", $messages);
        $this->fail($report);
    }

    /**
     * Filters messages to report.
     *
     * @param array $messages Messages to filter.
     *
     * @return array Filtered messages.
     */
    private function filterMesages(array $messages)
    {
        $filteredMessages = array();

        foreach ($messages as $message) {
            /* @var $message MarkupValidatorMessageInterface */
            $messageType = $message->getType();

            if ($messageType === MarkupValidatorMessageInterface::TYPE_UNDEFINED ||
                $messageType === MarkupValidatorMessageInterface::TYPE_INFO
            ) {
                continue;
            }

            if ($messageType === MarkupValidatorMessageInterface::TYPE_WARNING &&
                $this->ignoreWarnings() === true
            ) {
                continue;
            }

            if ($this->ignoreError($message->getSummary()) === true) {
                continue;
            }

            $filteredMessages[] = $message;
        }

        return $filteredMessages;
    }

    /**
     * Returns a boolean indicating whether messages count
     * is below the threshold or not.
     *
     * @param array $messages Messages to report about.
     *
     * @return boolean Whether messages count is below the threshold or not.
     */
    private function belowErrorCountThreshold(array $messages)
    {
        if (is_int($this->configuration[self::ERROR_COUNT_THRESHOLD_KEY]) === false) {
            throw new Exception(sprintf('Invalid «%s» config key.', self::ERROR_COUNT_THRESHOLD_KEY));
        }

        $threshold = $this->configuration[self::ERROR_COUNT_THRESHOLD_KEY];
        $belowThreshold = count($messages) <= $threshold;

        return $belowThreshold;
    }

    /**
     * Returns a boolean indicating whether the reporter ignores warnings or not.
     *
     * @return bool Whether the reporter ignores warnings or not.
     */
    private function ignoreWarnings()
    {
        if (is_bool($this->configuration[self::IGNORE_WARNINGS_CONFIG_KEY]) === false) {
            throw new Exception(sprintf('Invalid «%s» config key.', self::IGNORE_WARNINGS_CONFIG_KEY));
        }

        /* @var $ignoreWarnings bool */
        $ignoreWarnings = $this->configuration[self::IGNORE_WARNINGS_CONFIG_KEY];

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
        if (is_array($this->configuration[self::IGNORED_ERRORS_CONFIG_KEY]) === false) {
            throw new Exception(sprintf('Invalid «%s» config key.', self::IGNORED_ERRORS_CONFIG_KEY));
        }

        $ignoreError = false;

        if ($summary === null) {
            return $ignoreError;
        }

        $ignoredErrors = $this->configuration[self::IGNORED_ERRORS_CONFIG_KEY];
        foreach ($ignoredErrors as $ignoredError) {
            $erorIsIgnored = preg_match($ignoredError, $summary) === 1;
            if ($erorIsIgnored) {
                $ignoreError = true;
                break;
            }
        }

        return $ignoreError;
    }
}
