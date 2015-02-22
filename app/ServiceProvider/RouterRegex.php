<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\ServiceProvider;

use Shade\ServiceProviderInterface;
use Shade\Router\Regex;

/**
 * Router "Regex" Service Provider
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class RouterRegex implements ServiceProviderInterface
{
    /**
     * Instantiate Service
     *
     * @return \Shade\Router\Regex
     */
    public function instantiate()
    {
        return new Regex();
    }
}
