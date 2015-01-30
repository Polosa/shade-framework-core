<?php

/**
 * Shade
 *
 * @version 0.1
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
     * Assignments
     *
     * @var array
     */
    protected $assignments = array();

    /**
     * Constructor
     *
     * @param string $destinationPattern
     * @param string $controllerClassName
     * @param string $actionName
     * @param array  $assignments
     */
    public function __construct($destinationPattern, $controllerClassName, $actionName, array $assignments = array())
    {
        $this->destinationPattern = $destinationPattern;
        $this->controller = $controllerClassName;
        $this->action = $actionName;
        $this->assignments = $assignments;
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

    /**
     * Get assignments
     *
     * @return array
     */
    public function assignments()
    {
        return $this->assignments;
    }

    /**
     * Pass Service
     *
     * @param string $argumentName Argument name of associated action
     * @param string $serviceName  Registered Application Service name
     *
     * @return Mapping
     */
    public function pass($argumentName, $serviceName)
    {
        $this->assignments[$argumentName] = $serviceName;
        return $this;
    }
}
