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
     * Action arguments
     *
     * @var array
     */
    protected $args = array();

    /**
     * Constructor
     *
     * @param string $controllerClassName
     * @param string $actionName
     * @param array  $args
     */
    public function __construct($controllerClassName, $actionName, array $args = array())
    {
        $this->controller = $controllerClassName;
        $this->action = $actionName;
        $this->args = $args;
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

    /**
     * Get arguments
     *
     * @return array Arguments
     */
    public function args()
    {
        return $this->args;
    }
}
