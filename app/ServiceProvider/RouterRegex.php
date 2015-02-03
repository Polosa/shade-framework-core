<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\ServiceProvider;

use Shade\ServiceProviderInterface;
use Shade\App;
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
     * Application
     *
     * @var App
     */
    protected $app;

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
     * @return \Shade\Router\Regex
     */
    public function instantiate()
    {
        return new Regex('\\'.$this->app->getAppNamespace().'\\'.Regex::BASE_CONTROLLER);
    }
}
