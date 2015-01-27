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
class Regex extends Router
{
    /**
     * Routes
     *
     * @var array
     */
    protected $routes = array();

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

    /**
     * Build route
     *
     * @param \Shade\Request $request Request
     *
     * @throws \Shade\Exception
     *
     * @return \Shade\Route
     */
    public function route(Request $request)
    {
        if ($request instanceof Request\Web) {
            $server = $request->getServer();
            if (!isset($server['REQUEST_URI'])) {
                throw new Exception('REQUEST_URI is not set');
            }

            $urlComponents = explode('?', $server['REQUEST_URI']);
            $destination = reset($urlComponents);
            if ($destination && (strpos($destination, $request::SCRIPT_NAME) === 0)) {
                $destination = substr($destination, strlen($request::SCRIPT_NAME));
            }

            if (empty($destination) || $destination === '/') {
                return new Route($this->baseControllerClass.'\\'.self::DEFAULT_WEB_CONTROLLER, self::DEFAULT_ACTION);
            }

        } elseif ($request instanceof Request\Cli) {
            $argv = $request->getArgv();
            if (!isset($argv[1])) {
                return new Route($this->baseControllerClass.'\\'.self::DEFAULT_CLI_CONTROLLER, self::DEFAULT_ACTION);
            }
            $destination = $argv[1];
        } elseif ($request instanceof Request\Virtual) {
            if (!method_exists($request->getController(), $request->getAction())) {
                throw new Exception("Method {$request->getAction()} does not exists in class {$request->getAction()}");
            }
            $this->validateActionArguments($request->getController(), $request->getAction(), $request->getActionArgs());

            return new Route($request->getController(), $request->getAction(), $request->getActionArgs());
        } else {
            throw new Exception('Request type not supported');
        }

        $destination = trim($destination, '/');

        $routingFound = false;
        foreach ($this->routes as $urlPattern => $action) {
            $routingFound = preg_match($urlPattern, $destination, $matches);
            if ($routingFound) {
                $actionData = explode('::', $action, 2);
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

        if (!$routingFound) {
            throw new Exception("Routing not found for destination '{$destination}'");
        }

        $this->validateActionArguments($controllerClass, $action, $args);

        return new Route($controllerClass, $action, $args);
    }
}
