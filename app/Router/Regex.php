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
     * Routes
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Get Route for given destination
     *
     * @param $destination
     *
     * @throws \Shade\Exception
     *
     * @return Route
     */
    protected function getRoute($destination)
    {
        foreach ($this->routes as $urlPattern => $action) {
            if (preg_match($urlPattern, $destination, $matches)) {
                $actionData = explode('::', $action);
                if (count($actionData) !== 2) {
                    throw new Exception("Incorrectly defined action '{$action}' for destination pattern '{$urlPattern}'");
                }
                $controllerClass = reset($actionData);
                $action = end($actionData);
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
                        $args[$actionParameter->getPosition()] = $matches[$parameterName];
                    }
                }

                break;
            }
        }

        if (!isset($action)) {
            throw new Exception("Routing not found for destination '{$destination}'");
        }

        $this->validateActionArguments($controllerClass, $action, $args);

        return new Route($controllerClass, $action, $args);
    }

    /**
     * Add route
     *
     * @param string $urlPattern URL pattern
     * @param string $action     Controller class and action name separated by "::"
     *
     * @return \Shade\Router\Regex
     */
    public function addRoute($urlPattern, $action)
    {
        $this->routes[$urlPattern] = $action;
        return $this;
    }
}
