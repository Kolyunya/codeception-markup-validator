<?php

namespace Kolyunya\Codeception\Module;

use Exception;
use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupProviderInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupReporterInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorInterface;

/**
 * A module which validates page markup via a markup validator.
 */
class MarkupValidator extends Module
{
    const PROVIDER_CONFIG_KEY = 'provider';

    const VALIDATOR_CONFIG_KEY = 'validator';

    const REPORTER_CONFIG_KEY = 'reporter';

    const COMPONENT_CLASS_CONFIG_KEY = 'class';

    const COMPONENT_CONFIG_CONFIG_KEY = 'config';

    /**
     * {@inheritDoc}
     */
    protected $config = array(
        self::PROVIDER_CONFIG_KEY => array(
            self::COMPONENT_CLASS_CONFIG_KEY => 'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupProvider',
            self::COMPONENT_CONFIG_CONFIG_KEY => array(),
        ),
        self::VALIDATOR_CONFIG_KEY => array(
            self::COMPONENT_CLASS_CONFIG_KEY => 'Kolyunya\Codeception\Lib\MarkupValidator\W3CMarkupValidator',
            self::COMPONENT_CONFIG_CONFIG_KEY => array(),
        ),
        self::REPORTER_CONFIG_KEY => array(
            self::COMPONENT_CLASS_CONFIG_KEY => 'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter',
            self::COMPONENT_CONFIG_CONFIG_KEY => array(
                DefaultMarkupReporter::IGNORED_ERRORS_CONFIG_KEY => array(),
                DefaultMarkupReporter::IGNORE_WARNINGS_CONFIG_KEY => false,
            ),
        ),
    );

    /**
     * Markup provider.
     *
     * @var MarkupProviderInterface
     */
    private $provider;

    /**
     * Markup validator.
     *
     * @var MarkupValidatorInterface
     */
    private $validator;

    /**
     * Markup validation message reporter.
     *
     * @var MarkupReporterInterface
     */
    private $reporter;

    /**
     * {@inheritDoc}
     */
    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        parent::__construct($moduleContainer, $config);

        $this->initializeProvider();
        $this->initializeValidator();
        $this->initializeReporter();
    }

    /**
     * Validates page markup via a markup validator.
     * Allows to recongigure reporter component.
     *
     * @param array $reporterConfiguration Reporter configuration.
     */
    public function validateMarkup(array $reporterConfiguration = array())
    {
        $markup = $this->provider->getMarkup();
        $messages = $this->validator->validate($markup);

        $this->reporter->setConfiguration($reporterConfiguration);
        foreach ($messages as $message) {
            $this->reporter->report($message);
        }

        // Validation succeeded.
        $this->assertTrue(true);
    }

    /**
     * Initializes markup provider.
     */
    private function initializeProvider()
    {
        $providerName = self::PROVIDER_CONFIG_KEY;
        $providerClass = $this->getComponentClass($providerName);
        $providerConfig = $this->getComponentConfig($providerName);
        $this->provider = new $providerClass($this->moduleContainer, $providerConfig);
        $providerInterface = 'Kolyunya\Codeception\Lib\MarkupValidator\MarkupProviderInterface';
        $this->validateComponentInstance($this->provider, $providerInterface, $providerName);
    }

    /**
     * Initializes markup validator.
     */
    private function initializeValidator()
    {
        $validatorName = self::VALIDATOR_CONFIG_KEY;
        $validatorClass = $this->getComponentClass($validatorName);
        $validatorConfig = $this->getComponentConfig($validatorName);
        $this->validator = new $validatorClass($validatorConfig);
        $validatorInterface = 'Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorInterface';
        $this->validateComponentInstance($this->validator, $validatorInterface, $validatorName);
    }

    /**
     * Initializes markup validator.
     */
    private function initializeReporter()
    {
        $reporterName = self::REPORTER_CONFIG_KEY;
        $reporterClass = $this->getComponentClass($reporterName);
        $reporterConfig = $this->getComponentConfig($reporterName);
        $this->reporter = new $reporterClass($reporterConfig);
        $reporterInterface = 'Kolyunya\Codeception\Lib\MarkupValidator\MarkupReporterInterface';
        $this->validateComponentInstance($this->reporter, $reporterInterface, $reporterName);
    }

    /**
     * Returns component class name.
     *
     * @param string $componentName Component name.
     *
     * @return string Component class name.
     */
    private function getComponentClass($componentName)
    {
        $componentClassKey = self::COMPONENT_CLASS_CONFIG_KEY;
        if (isset($this->config[$componentName][$componentClassKey]) === false ||
            is_string($this->config[$componentName][$componentClassKey]) === false
        ) {
            $errorMessage = sprintf('Invalid class configuration of component «%s».', $componentName);
            throw new Exception($errorMessage);
        }

        $componentClass = $this->config[$componentName][$componentClassKey];

        return $componentClass;
    }

    /**
     * Returns component configuration parameters.
     *
     * @param string $componentName Component name.
     *
     * @return string Component configuration parameters.
     */
    private function getComponentConfig($componentName)
    {
        $componentConfig = array();

        $componentConfigKey = self::COMPONENT_CONFIG_CONFIG_KEY;
        if (isset($this->config[$componentName][$componentConfigKey]) === true) {
            if (is_array($this->config[$componentName][$componentConfigKey]) === true) {
                $componentConfig = $this->config[$componentName][$componentConfigKey];
            } else {
                $errorMessage = sprintf('Invalid configuration of component «%s».', $componentName);
                throw new Exception($errorMessage);
            }
        }

        return $componentConfig;
    }

    /**
     * Ensures that a component is an instance of a specifi interface.
     *
     * @param object $component Component instance to validate.
     * @param string $interface Interface to validate component instance against.
     * @param string $componentName Component name. User for error logging.
     *
     * @throws Exception When `component` is not an instance of the `interface`.
     */
    private function validateComponentInstance($component, $interface, $componentName)
    {
        if (($component instanceof $interface) === false) {
            $componentClass = get_class($component);
            $errorMessage = vsprintf('Invalid class «%s» provided for component «%s».', array(
                $componentClass,
                $componentName,
            ));
            throw new Exception($errorMessage);
        }
    }
}
