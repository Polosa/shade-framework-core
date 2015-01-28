<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

use Shade\Router\RouterInterface;

/**
 * Service Provider
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ServiceProvider
{
    /**
     * Application
     *
     * @var \Shade\App
     */
    protected $app;

    /**
     * Router
     *
     * @var \Shade\Router\RouterInterface
     */
    protected $router;

    /**
     * Views
     *
     * @var \Shade\View[]
     */
    protected $views = array();

    /**
     * Service creation in progress
     *
     * @var bool
     */
    protected $inProgress = false;

    /**
     * Constructor
     *
     * @param \Shade\App $app Application
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Is getting Service in progress
     *
     * @return bool
     */
    public function inProgress()
    {
        return $this->inProgress;
    }

    /**
     * Get Application
     *
     * @return \Shade\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Get Router
     *
     * @param string|null $implementation Class name of the Router
     *
     * @throws \Exception
     *
     * @return \Shade\Router\RouterInterface
     */
    public function getRouter($implementation = null)
    {
        if (!$implementation) {
            $config = $this->app->getConfig();
            $implementation = $config['services']['router'];
        }

        if (!($this->router instanceof RouterInterface)) {
            try {
                $this->inProgress = true;
                $this->router = new $implementation($this);
                $this->inProgress = false;
            } catch (\Exception $e) {
                $this->inProgress = false;
                throw $e;
            }
        }

        return $this->router;
    }

    /**
     * Get View
     *
     * @param string|null $implementation Class name of the View
     *
     * @throws \Exception
     *
     * @return \Shade\View
     */
    public function getView($implementation = null)
    {
        if (!$implementation) {
            $config = $this->app->getConfig();
            $implementation = $config['services']['view'];
        }

        if (isset($this->views[$implementation]) && $this->views[$implementation] instanceof View) {
            return $this->views[$implementation];
        }

        if (class_exists($implementation)) {
            try {
                $this->inProgress = true;
                $view = new $implementation($this);
                $this->inProgress = false;
            } catch (\Exception $e) {
                $this->inProgress = false;
                throw $e;
            }

            if ($view instanceof View) {
                $this->views[$implementation] = $view;

                return $view;
            }
        }

        throw new Exception('Requested View implementation should be a fully qualified name of the class that inherits \Shade\View');
    }
}
