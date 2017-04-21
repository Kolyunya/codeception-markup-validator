<?php

namespace Kolyunya\Codeception\Lib\MarkupValidator;

use Kolyunya\Codeception\Lib\MarkupValidator\MarkupValidatorMessageInterface;

/**
 * Base markup validator message.
 */
class MarkupValidatorMessage implements MarkupValidatorMessageInterface
{
    /**
     * Message type.
     *
     * @var integer
     */
    protected $type;

    /**
     * Message summary.
     *
     * @var string
     */
    protected $summary;

    /**
     * Message details.
     *
     * @var string
     */
    protected $details;

    /**
     * Related markup.
     *
     * @var string
     */
    protected $markup;

    /**
     * Constructs a markup validator message.
     */
    public function __construct(
        $type = self::TYPE_UNDEFINED,
        $summary = null,
        $details = null,
        $markup = null
    ) {
        $this->type = $type;
        $this->summary = $summary;
        $this->details = $details;
        $this->markup = $markup;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return vsprintf($this->getStringTemplate(), array(
            $this->getType() ?: 'unavailable',
            $this->getSummary() ?: 'unavailable',
            $this->getDetails() ?: 'unavailable',
            $this->getMarkup() ?: 'unavailable',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * {@inheritDoc}
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * {@inheritDoc}
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * Returns string representation template.
     *
     * @return string String representation template.
     */
    protected function getStringTemplate()
    {
        return
            <<<TXT
Markup validator message:
Type: %s
Summary: %s
Details: %s
Markup: %s

TXT
        ;
    }
}
