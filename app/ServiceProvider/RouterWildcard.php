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
     * Path to Application's entry point (relative to the document root; e.g. /index.php)
     *
     * @var string
     */
    protected $entryPoint;

    /**
     * Constructor
     *
     * @param App    $app        Application
     * @param string $entryPoint Path to Application's entry point (relative to the document root; e.g. /index.php)
     */
    public function __construct(App $app, $entryPoint = null)
    {
        $this->app = $app;
        $this->entryPoint = $entryPoint ? $entryPoint : $_SERVER['SCRIPT_NAME'];
    }

    /**
     * Instantiate Service
     *
     * @return \Shade\Router\Wildcard
     */
    public function instantiate()
    {
        $router = new Wildcard($this->entryPoint);
        $router->setLogger($this->app->getLogger());
        return $router;
    }
}
