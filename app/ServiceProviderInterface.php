<?php

/**
 * Shade
 *
 * @version 0.1
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
