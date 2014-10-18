<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * View
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
abstract class View
{
    /**
     * Templates directory
     */
    const TEMPLATES_DIR = 'templates';

    /**
     * Service Provider
     *
     * @var \Shade\ServiceProvider
     */
    protected $serviceProvider;

    /**
     * Templates path
     *
     * @var string
     */
    protected $templatesPath;

    /**
     * Helpers
     *
     * @var array
     */
    protected static $helpers = array();

    /**
     * Constructor
     *
     * @param \Shade\ServiceProvider $serviceProvider
     */
    public function __construct(ServiceProvider $serviceProvider)
    {
        $this->serviceProvider = $serviceProvider;
        $this->templatesPath = $this->serviceProvider->getApp()->getAppDir().'/'.self::TEMPLATES_DIR.'/';
    }

    /**
     * Get templates path
     *
     * @return string
     */
    public function getTemplatesPath()
    {
        return $this->templatesPath;
    }

    /**
     * Set helper function
     *
     * @param string   $name     Name
     * @param callable $callback Callback function
     *
     * @return \Shade\View
     */
    protected function setHelper($name, $callback)
    {
        self::$helpers[$name] = $callback;

        return $this;
    }

    /**
     * Get helper
     *
     * @param string $name Helper name
     *
     * @return callable
     */
    protected static function getHelper($name)
    {
        return self::$helpers[$name];
    }

    /**
     * Render template
     *
     * @param string|array $__templates Path to template or array of paths to template and layouts
     * @param array        $__data      Data for templates
     *
     * @return string
     */
    abstract public function render($__templates, array $__data = array());
}
