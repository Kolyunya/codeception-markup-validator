<?php

namespace Kolyunya\Codeception\Module;

use Exception;
use stdClass;
use Codeception\Lib\InnerBrowser;
use Codeception\Module\WebDriver;
use GuzzleHttp\Client;

/**
 * A module which validates current page markup via the W3C Markup Validation Service.
 * Requires either the `PhpBrowser` or the `WebDriver` module.
 */
class MarkupValidator extends \Codeception\Module
{
    const W3C_MARKUP_VALIDATION_SERVICE_BASE_URI = 'https://validator.w3.org';

    const W3C_MARKUP_VALIDATION_SERVICE_ENDPOINT = '/nu/';

    /**
     * Validates current page markup via the W3C Markup Validation Service.
     *
     * @param bool $ignoreWarnings Whether to ignore warnings or not.
     */
    public function validateMarkup($ignoreWarnings = false)
    {
        $markup = $this->getCurrentPageMarkup();
        $validationData = $this->sendMarkupValidationRequest($markup);
        foreach ($validationData->messages as $message) {
            $this->processMarkupValidationMessage($message, $ignoreWarnings);
        }
    }

    /**
     * Returns current page markup.
     *
     * @return string Current page markup.
     */
    protected function getCurrentPageMarkup()
    {
        try {
            $markup = $this->getMarkupFromPhpBrowser();
            return $markup;
        } catch (Exception $exception) {
            // Wasn't able to get markup from the PhpBrowser.
        }

        try {
            $markup = $this->getMarkupFromWebDriver();
            return $markup;
        } catch (Exception $exception) {
            // Wasn't able to get markup from the WebDriver.
        }

        throw new Exception('Unable to obtain current page markup.');
    }

    /**
     * Send a markup validation request to the W3C Markup Validation Service
     * and returns response data.
     *
     * @param string $markup Page markup to validate.
     * @return stdClass W3C Markup Validation Service response data.
     */
    protected function sendMarkupValidationRequest($markup)
    {
        $сlient = new Client([
            'base_uri' => self::W3C_MARKUP_VALIDATION_SERVICE_BASE_URI,
        ]);
        $reponse = $сlient->post(self::W3C_MARKUP_VALIDATION_SERVICE_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'text/html; charset=UTF-8;',
            ],
            'query' => [
                'out' => 'json',
            ],
            'body' => $markup,
        ]);
        $responseContents = $reponse->getBody()->getContents();
        $responseData = json_decode($responseContents);
        if ($responseData === null) {
            throw new Exception('Unable to parse W3C Markup Validation Service response.');
        }

        return $responseData;
    }

    /**
     * Processes a document markup validation message.
     *
     * @param stdClass $message Markup validation message.
     * @param bool $ignoreWarnings Whether to ignore warnings or not.
     */
    protected function processMarkupValidationMessage(stdClass $message, $ignoreWarnings)
    {
        $type = $message->type;
        $summary = $message->message;
        $details = isset($message->extract)
                    ? $message->extract
                    : 'unavailable';
        if ($type === 'error' ||
            $type === 'warning' && !$ignoreWarnings
        ) {
            $this->reportMarkupValidationError($summary, $details);
        }
    }

    /**
     * Reports a document markup validation error.
     *
     * @param string $summary Markup validation error summary.
     * @param string $details Markup validation error details.
     */
    protected function reportMarkupValidationError($summary, $details)
    {
        $template = 'Markup validation error. %s. Details: %s';
        $message = sprintf($template, $summary, $details);
        $this->fail($message);
    }

    /**
     * Returns current page markup form the PhpBrowser.
     *
     * @return string Current page markup.
     */
    private function getMarkupFromPhpBrowser()
    {
        $moduleName = 'PhpBrowser';
        if (!$this->hasModule($moduleName)) {
            throw new Exception(sprintf('"%s" module is not enabled.'));
        }

        /* @var $phpBrowser InnerBrowser */
        $phpBrowser = $this->getModule($moduleName);
        $markup = $phpBrowser->_getResponseContent();

        return $markup;
    }

    /**
     * Returns current page markup form the WebDriver.
     *
     * @return string Current page markup.
     */
    private function getMarkupFromWebDriver()
    {
        $moduleName = 'WebDriver';
        if (!$this->hasModule($moduleName)) {
            throw new Exception(sprintf('"%s" module is not enabled.'));
        }

        /* @var $webDriver WebDriver */
        $webDriver = $this->getModule($moduleName);
        $markup = $webDriver->webDriver->getPageSource();

        return $markup;
    }
}
