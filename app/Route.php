<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Route
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Route
{
    /**
     * Controller class name
     *
     * @var string
     */
    protected $controller;

    /**
     * Action name
     *
     * @var string
     */
    protected $action;

    /**
     * Constructor
     *
     * @param string $controllerClassName
     * @param string $actionName
     */
    public function __construct($controllerClassName, $actionName)
    {
        $this->controller = $controllerClassName;
        $this->action = $actionName;
    }

    /**
     * Get Controller name
     *
     * @return string
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * Get Action name
     *
     * @return string Action name
     */
    public function action()
    {
        return $this->action;
    }
}
