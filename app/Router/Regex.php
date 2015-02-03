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
            if (preg_match($urlPattern, $destination, $matches)) {

                $controllerClass = $mapping->controller();
                $action = $mapping->action();

                if (!class_exists($controllerClass)) {
                    throw new Exception("Controller '{$controllerClass}' is not found");
                }
                if (!method_exists($controllerClass, $action)) {
                    throw new Exception("Action '{$action}' is not found in controller '{$controllerClass}'");
                }

                $args = array();
                $reflectionAction = new \ReflectionMethod($controllerClass, $action);
                $actionParameters = $reflectionAction->getParameters();
                foreach ($actionParameters as $actionParameter) {
                    $parameterName = $actionParameter->getName();
                    if (array_key_exists($parameterName, $matches)) {
                        $args[$parameterName] = $matches[$parameterName];
                    }
                }

                break;
            }
        }

        if (!isset($action)) {
            throw new Exception("Route mapping not found for destination '{$destination}'");
        }

        //TODO validation in App?
        //$this->validateActionArguments($controllerClass, $action, $args);

        return new Route($controllerClass, $action, $args);
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
