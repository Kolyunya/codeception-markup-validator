<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Exception;
use GuzzleHttp\Client;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\W3CMarkupValidatorMessage;

class W3CMarkupValidator implements MarkupValidatorInterface
{
    /**
     * Base URI of the W3C Markup Validator Service.
     */
    const BASE_URI = 'https://validator.w3.org/';

    /**
     * Endpoint of the W3C Markup Validator Service.
     */
    const ENDPOINT = '/nu/';

    /**
     * Configuration parameters.
     *
     * @var array
     */
    private $config;

    /**
     * HTTP client used to communicate with the W3C Markup Validation Service.
     *
     * @var Client
     */
    private $httpClient;

    /**
     * Parameters of a HTTP request to the W3C Markup Validation Service.
     *
     * @var array
     */
    private $httpRequestParameters;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;

        $this->initializeHttpClient();
        $this->initializeHttpRequestParameters();
    }

    /**
     * {@inheritDoc}
     */
    public function validate($markup)
    {
        $validationData = $this->getValidationData($markup);
        $validationMessages = $this->getValidationMessages($validationData);

        return $validationMessages;
    }

    /**
     * Initializes HTTP client used to communicate with the W3C Markup Validation Service.
     */
    private function initializeHttpClient()
    {
        $this->httpClient = new Client([
            'base_uri' => self::BASE_URI,
        ]);
    }

    /**
     * Initializes parameters of a HTTP request to the W3C Markup Validation Service.
     */
    private function initializeHttpRequestParameters()
    {
        $this->httpRequestParameters = array(
            'headers' => array(
                'Content-Type' => 'text/html; charset=UTF-8;',
            ),
            'query' => array(
                'out' => 'json',
            ),
        );
    }

    /**
     * Sends a validation request to a W3C Markup Validation Service
     * and returns decoded validation data.
     *
     * @param string $markup Markup to get validation data for.
     * @return array Validation data for provided markup.
     */
    private function getValidationData($markup)
    {
        $this->httpRequestParameters['body'] = $markup;

        $reponse = $this->httpClient->post(self::ENDPOINT, $this->httpRequestParameters);
        $responseData = $reponse->getBody()->getContents();
        $validationData = json_decode($responseData, true);
        if ($validationData === null) {
            $errorMessageTemplate = "Unable to parse W3C Markup Validation Service response. Response data:\n%s\n";
            throw new Exception(sprintf($errorMessageTemplate, $responseData));
        }

        return $validationData;
    }

    /**
     * Parses validation data and returns validation messages.
     *
     * @param array $validationData Validation data.
     * @return MarkupValidatorMessageInterface[] Validation messages.
     */
    private function getValidationMessages(array $validationData)
    {
        $messages = array();
        $messagesData = $validationData['messages'];
        foreach ($messagesData as $messageData) {
            $message = new W3CMarkupValidatorMessage($messageData);
            $messages[] = $message;
        }

        return $messages;
    }
}
