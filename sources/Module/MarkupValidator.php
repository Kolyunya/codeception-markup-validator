<?php

namespace Kolyunya\Codeception\Module;

use Exception;
use ReflectionClass;
use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Kolyunya\Codeception\Lib\Base\ComponentInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupProviderInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupReporterInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorInterface;

/**
 * A module which validates page markup via a markup validator.
 */
class MarkupValidator extends Module
{

    const COMPONENT_CLASS_CONFIG_KEY = 'class';

    const COMPONENT_CONFIG_CONFIG_KEY = 'config';

    const PROVIDER_INTERFACE = 'Kolyunya\Codeception\Lib\MarkupValidator\MarkupProviderInterface';
    const PROVIDER_CONFIG_KEY = 'provider';

    const VALIDATOR_INTERFACE = 'Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorInterface';
    const VALIDATOR_CONFIG_KEY = 'validator';

    const REPORTER_INTERFACE = 'Kolyunya\Codeception\Lib\MarkupValidator\MarkupReporterInterface';
    const REPORTER_CONFIG_KEY = 'reporter';

    const PRINTER_INTERFACE = 'Kolyunya\Codeception\Lib\MarkupValidator\MessagePrinterInterface';
    const PRINTER_CONFIG_KEY = 'printer';

    /**
     * {@inheritDoc}
     */
    protected $config = array(
        self::PROVIDER_CONFIG_KEY => array(
            self::COMPONENT_CLASS_CONFIG_KEY => 'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupProvider',
        ),
        self::VALIDATOR_CONFIG_KEY => array(
            self::COMPONENT_CLASS_CONFIG_KEY => 'Kolyunya\Codeception\Lib\MarkupValidator\W3CMarkupValidator',
        ),
        self::REPORTER_CONFIG_KEY => array(
            self::COMPONENT_CLASS_CONFIG_KEY => 'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter',
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
        $this->reporter->report($messages);

        // Validation succeeded.
        $this->assertTrue(true);
    }

    /**
     * Initializes markup provider.
     */
    private function initializeProvider()
    {
        $this->provider = $this->instantiateComponent(
            self::PROVIDER_CONFIG_KEY,
            self::PROVIDER_INTERFACE,
            array(
                $this->moduleContainer,
            )
        );
    }

    /**
     * Initializes markup validator.
     */
    private function initializeValidator()
    {
        $this->validator = $this->instantiateComponent(
            self::VALIDATOR_CONFIG_KEY,
            self::VALIDATOR_INTERFACE
        );
    }

    /**
     * Initializes markup reporter.
     */
    private function initializeReporter()
    {
        $this->reporter = $this->instantiateComponent(
            self::REPORTER_CONFIG_KEY,
            self::REPORTER_INTERFACE
        );
    }

    /**
     * Instantiates and returns a module component.
     *
     * @param string $componentName Component name.
     * @param string $interface An interface component must implement.
     * @param array $arguments Component's constructor arguments.
     *
     * @throws Exception When component does not implement expected interface.
     *
     * @return object Instance of a module component.
     */
    private function instantiateComponent($componentName, $interface, array $arguments = array())
    {
        $componentClass = $this->getComponentClass($componentName);
        $componentReflectionClass = new ReflectionClass($componentClass);
        if ($componentReflectionClass->implementsInterface($interface) === false) {
            $errorMessageTemplate = 'Invalid class «%s» provided for component «%s». It must implement «%s».';
            $errorMessage = sprintf($errorMessageTemplate, $componentClass, $componentName, $interface);
            throw new Exception($errorMessage);
        }

        /* @var $component ComponentInterface */
        $component = $componentReflectionClass->newInstanceArgs($arguments);
        $componentConfiguration = $this->getComponentConfiguration($componentName);
        $component->setConfiguration($componentConfiguration);

        return $component;
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
     * @return array Component configuration parameters.
     */
    private function getComponentConfiguration($componentName)
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
}
