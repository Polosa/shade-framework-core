<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Service Provider interface
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
interface ServiceProviderInterface
{
    /**
     * Instantiate Service
     *
     * @return mixed Service
     */
    public function instantiate();
}
