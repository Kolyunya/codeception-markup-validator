<?php

namespace Kolyunya\Codeception\Lib\Base;

use Kolyunya\Codeception\Lib\Base\ComponentInterface;

abstract class Component implements ComponentInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getClassName()
    {
        $className = get_called_class();

        return $className;
    }
}
