<?php

namespace Kolyunya\Codeception\Module;

use Exception;
use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupProviderInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupReporterInterface;
use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorInterface;

/**
 * A module which validates page markup via a markup validator.
 */
class MarkupValidator extends Module
{
    /**
     * {@inheritDoc}
     */
    protected $config = array(
        'provider' => array(
            'class' => 'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupProvider',
            'config' => array(),
        ),
        'validator' => array(
            'class' => 'Kolyunya\Codeception\Lib\MarkupValidator\W3CMarkupValidator',
            'config' => array(),
        ),
        'reporter' => array(
            'class' => 'Kolyunya\Codeception\Lib\MarkupValidator\DefaultMarkupReporter',
            'config' => array(
                'ignoredErrors' => array(),
                'ignoreWarnings' => false,
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
     */
    public function validateMarkup()
    {
        $markup = $this->provider->getMarkup();

        $messages = $this->validator->validate($markup);
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
        $name = 'provider';
        $interface = 'Kolyunya\Codeception\Lib\MarkupValidator\MarkupProviderInterface';
        $componentClass = $this->config[$name]['class'];
        $componentConfig = $this->config[$name]['config'];
        $component = new $componentClass($this->moduleContainer, $componentConfig);
        if (($component instanceof $interface) === false) {
            $errorMessage = sprintf('Invalid component class provided: «%s».', $componentClass);
            throw new Exception($errorMessage);
        }

        $this->provider = $component;
    }

    /**
     * Initializes markup validator.
     */
    private function initializeValidator()
    {
        $this->validator = $this->makeComponent(
            'validator',
            'Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorInterface'
        );
    }

    /**
     * Initializes markup validator.
     */
    private function initializeReporter()
    {
        $this->reporter = $this->makeComponent(
            'reporter',
            'Kolyunya\Codeception\Lib\MarkupValidator\MarkupReporterInterface'
        );
    }

    /**
     * Constructs a validator component.
     *
     * @param string $name Component name.
     * @param string $interface Component interface.
     *
     * @return object Component instance.
     */
    private function makeComponent($name, $interface)
    {
        $componentClass = $this->config[$name]['class'];
        $componentConfig = $this->config[$name]['config'];
        $component = new $componentClass($componentConfig);
        if (($component instanceof $interface) === false) {
            $errorMessage = sprintf('Invalid component class provided: «%s».', $componentClass);
            throw new Exception($errorMessage);
        }

        return $component;
    }
}
