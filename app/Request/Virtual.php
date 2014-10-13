<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Request;

use Shade\Request;
use Shade\ServiceProvider;

/**
 * Virtual Request
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Virtual extends Request
{
    /**
     * Requested controller class name
     *
     * @var string
     */
    protected $controller;

    /**
     * Requested action
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
     * @param \Shade\ServiceProvider $serviceProvider
     * @param string                 $controller
     * @param string                 $action
     * @param array                  $args
     * @param array                  $get
     */
    public function __construct(
        ServiceProvider $serviceProvider,
        $controller,
        $action,
        array $args = array(),
        array $get = array()
    ) {
        $this->serviceProvider = $serviceProvider;
        $this->controller = $controller;
        $this->action = $action;
        $this->args = $args;
        $this->get = $get;
    }

    /**
     * Get requested controller class name
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get requested action name
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get action arguments
     *
     * @return array
     */
    public function getActionArgs()
    {
        return $this->args;
    }
}
