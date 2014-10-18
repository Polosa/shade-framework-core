<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Service Provider
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ServiceProvider
{
    /**
     * Default View Implementation
     */
    const DEFAULT_VIEW_IMPLEMENTATION = '\Shade\View\Php';

    /**
     * Application
     *
     * @var \Shade\App
     */
    protected $app;

    /**
     * Router
     *
     * @var \Shade\Router
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
     * @return \Shade\Router
     */
    public function getRouter()
    {
        if (!($this->router instanceof Router)) {
            try {
                $this->inProgress = true;
                $this->router = new Router($this);
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
     * @param string $implementation Class name of the View
     *
     * @return \Shade\View
     */
    public function getView($implementation = self::DEFAULT_VIEW_IMPLEMENTATION)
    {
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
