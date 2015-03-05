<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Config
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Config extends NestedContainer
{
    /**
     * Merge with Config
     *
     * @param self $config Config to merge with
     *
     * @return self
     */
    public function merge($config)
    {
        return parent::merge($config);
    }

    /**
     * Overwrite Config
     *
     * @param self $config Config to overwrite by
     *
     * @return self
     */
    public function overwrite($config)
    {
        return parent::overwrite($config);
    }
}