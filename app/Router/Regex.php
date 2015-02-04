<?php

/**
 * Shade
 *
 * @version 0.1
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
     * @var Route\Mapping[]
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
     * Validate Route Mapping
     *
     * @param \Shade\Route\Mapping $mapping Route Mapping
     *
     * @throws \Shade\Exception
     */
    protected function validateRouteMapping(Route\Mapping $mapping)
    {
        if (!class_exists($mapping->controller())) {
            throw new Exception("Controller '{$mapping->controller()}' not found}");
        }

        if (!method_exists($mapping->controller(), $mapping->action())) {
            throw new Exception("Action '{$mapping->action()}' does not exist in controller '{$mapping->controller()}'");
        }
    }

    /**
     * Add route mapping
     *
     * @param string $destinationPattern Destination pattern
     * @param string $pointer            Controller class and action name separated by "::"
     *
     * @throws Exception
     *
     * @return \Shade\Route\Mapping
     */
    public function addMapping($destinationPattern, $pointer) //TODO pass controller and action separately?
    {
        $actionData = explode('::', $pointer);
        if (count($actionData) !== 2) {
            throw new Exception("Incorrectly defined pointer '{$pointer}' for destination pattern '{$destinationPattern}'");
        }
        $controllerClass = reset($actionData);
        $actionName = end($actionData);

        $mapping = new Route\Mapping($destinationPattern, $controllerClass, $actionName);
        $this->routeMapping[$destinationPattern] = $mapping;
        $this->reverseMapping[$pointer] = $mapping;

        return $mapping;
    }
}
