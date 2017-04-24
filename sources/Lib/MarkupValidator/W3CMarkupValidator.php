<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Exception;
use GuzzleHttp\Client;
use Kolyunya\Codeception\Lib\Base\Component;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\W3CMarkupValidatorMessage;

class W3CMarkupValidator extends Component implements MarkupValidatorInterface
{
    const BASE_URI_CONFIG_KEY = 'baseUri';

    const ENDPOINT_CONFIG_KEY = 'endpoint';

    /**
     * Configuration parameters.
     *
     * @var array
     */
    private $config = array(
        self::BASE_URI_CONFIG_KEY => 'https://validator.w3.org/',
        self::ENDPOINT_CONFIG_KEY => '/nu/',
    );

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
        $this->config = array_merge($this->config, $config);

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
            'base_uri' => $this->config[self::BASE_URI_CONFIG_KEY],
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

        $reponse = $this->httpClient->post(
            $this->config[self::ENDPOINT_CONFIG_KEY],
            $this->httpRequestParameters
        );
        $responseData = $reponse->getBody()->getContents();
        $validationData = json_decode($responseData, true);
        if ($validationData === null) {
            $errorMessageTemplate = "Unable to parse W3C Markup Validation Service response.";
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
