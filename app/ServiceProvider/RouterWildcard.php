<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\ServiceProvider;

use Shade\App;
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
     * Constructor
     *
     * @param App $app Application
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Instantiate Service
     *
     * @return \Shade\Router\Wildcard
     */
    public function instantiate()
    {
        $router = new Wildcard();
        $router->setLogger($this->app->getLogger());
        return $router;
    }
}
