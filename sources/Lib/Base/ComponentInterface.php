<?php

namespace Kolyunya\Codeception\Lib\Base;

interface ComponentInterface
{
    /**
     * Returns fully qualified name of the component class.
     *
     * @return string Fully qualified name of the component class.
     */
    public static function getClassName();
}
