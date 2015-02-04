<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Request;

use Shade\Request;

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
     * Constructor
     *
     * @param string $controller
     * @param string $action
     * @param array  $get
     */
    public function __construct(
        $controller,
        $action,
        array $get = array()
    ) {
        $this->controller = $controller;
        $this->action = $action;
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
}
