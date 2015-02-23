<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Route;

/**
 * Route mapping
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Mapping
{
    /**
     * Destination pattern
     *
     * @var string
     */
    protected $destinationPattern;

    /**
     * Controller class
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
     * @param string $destinationPattern
     * @param string $controllerClassName
     * @param string $actionName
     */
    public function __construct($destinationPattern, $controllerClassName, $actionName)
    {
        $this->destinationPattern = $destinationPattern;
        $this->controller = $controllerClassName;
        $this->action = $actionName;
    }

    /**
     * Get destination pattern
     *
     * @return string
     */
    public function destinationPattern()
    {
        return $this->destinationPattern;
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
     * @return string
     */
    public function action()
    {
        return $this->action;
    }
}
