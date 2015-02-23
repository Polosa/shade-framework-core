<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Router;

use Shade\Router;
use Shade\Request;
use Shade\Route;
use Shade\Exception;

/**
 * Regex Router
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Regex extends Router implements RouterCuInterface
{
    /**
     * Route mapping
     *
     * @var Route\Mapping[]
     */
    protected $routeMapping = array();

    /**
     * Reverse mapping
     *
     * @var array
     */
    protected $reverseMapping = array();

    /**
     * Get Route for given destination
     *
     * @param string $destination Destination
     *
     * @throws \Shade\Exception
     *
     * @return Route
     */
    protected function getRoute($destination)
    {
        foreach ($this->routeMapping as $urlPattern => $mapping) {
            if ($mappingFound = preg_match($urlPattern, $destination, $matches)) {
                $this->validateRouteMapping($mapping);
                break;
            }
        }

        if (empty($mappingFound)) {
            throw new Exception("Route mapping not found for destination '{$destination}'");
        }

        return new Route($mapping->controller(), $mapping->action());
    }

    /**
     * Add route mapping
     *
     * @param string $destinationPattern Destination pattern
     * @param string $controllerClass    Controller class
     * @param string $actionName         Action name
     *
     * @return \Shade\Route\Mapping
     *
     * @throws Exception
     */
    public function addMapping($destinationPattern, $controllerClass, $actionName)
    {
        $mapping = new Route\Mapping($destinationPattern, $controllerClass, $actionName);
        $this->routeMapping[$destinationPattern] = $mapping;
        $this->reverseMapping[$controllerClass][$actionName] = $mapping;

        return $mapping;
    }

    /**
     * Build Destination
     *
     * @param string $controller Controller name
     * @param string $action     Action name
     * @param array  $args       Action arguments
     *
     * @throws \Shade\Exception
     *
     * @return string
     */
    protected function buildDestination($controller, $action, array $args = array())
    {
        if (!isset($this->reverseMapping[$controller][$action])) {
            throw new Exception("Route mapping not found for controller '{$controller}', action '{$action}'");
        }

        /**
         * @var Route\Mapping $mapping
         */
        $mapping = $this->reverseMapping[$controller][$action];
        $destinationPattern = $mapping->destinationPattern();

        //TODO fix or remove this Router implementation

        return $destinationPattern;
    }
}
