<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\ServiceProvider;

use Shade\ServiceProviderInterface;
use Shade\Router\Wildcard;

/**
 * Router "Wildcard" Service Provider
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class RouterWildcard implements ServiceProviderInterface
{
    /**
     * Instantiate Service
     *
     * @return \Shade\Router\Wildcard
     */
    public function instantiate()
    {
        return new Wildcard();
    }
}
