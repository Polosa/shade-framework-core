<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Router;

use Shade\Request;

/**
 * Router interface
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
interface RouterInterface
{
    /**
     * Build route
     *
     * @param \Shade\Request $request Request
     *
     * @return \Shade\Route
     */
    public function route(Request $request);

    /**
     * Build URL
     *
     * @param string $controller Controller name
     * @param string $action     Action name
     * @param array  $args       Action arguments
     * @param array  $get        GET-parameters
     *
     * @return string
     */
    public function buildUrl($controller, $action, array $args = array(), array $get = array());
}
