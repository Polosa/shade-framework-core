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
 * Automatic Router
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Automatic extends Router
{
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

            $destination = (empty($destination) || $destination === '/') ? $request::DEFAULT_DESTINATION : $destination;
            $defaultController = self::DEFAULT_WEB_CONTROLLER;
        } elseif ($request instanceof Request\Cli) {
            $argv = $request->getArgv();
            $destination = isset($argv[1]) ? $argv[1] : $request::DEFAULT_DESTINATION;
            $defaultController = self::DEFAULT_CLI_CONTROLLER;
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

        $parts = explode('/', $destination);
        $controllerPath = $this->baseControllerPath;
        $controllerClass = $this->baseControllerClass;

        // Determine the controller directory
        foreach ($parts as $part) {
            $part = ucfirst($part);

            $tryPath = $controllerPath.$part;

            if (is_dir($tryPath)) {
                $controllerPath = $tryPath.'/';
                $controllerClass .= '\\'.$part;
                array_shift($parts);
                continue;
            } else {
                break;
            }
        }

        // Determine the controller file
        if (is_file($tryPath.'.php')) {
            $controllerFile = $part;
            array_shift($parts);
        } elseif (is_file("{$controllerPath}{$defaultController}.php")) {
            $controllerFile = $defaultController;
        } else {
            throw new Exception("Controller not found for route '{$destination}'");
        }
        $controllerClass .= '\\'.$controllerFile;

        // Looking for action and arguments
        $tryActions = array();
        if (!empty($parts)) {
            $tryActions['custom'] = reset($parts).self::ACTION_SUFFIX;
        }
        $tryActions['default'] = self::DEFAULT_ACTION;
        foreach ($tryActions as $actionType => $tryAction) {
            if (method_exists($controllerClass, $tryAction)) {
                $action = $tryAction;
                if ($actionType != 'default') {
                    array_shift($parts);
                }
                break;
            }
        }
        if (empty($action)) {
            if (count($tryActions) == 2) {
                throw new Exception("Actions '{$tryActions['custom']}' or '{$tryActions['default']}' not found in controller '$controllerClass' for destination '$destination'");
            } else {
                throw new Exception("Action '{$tryActions['default']}' not found in controller '$controllerClass' for destination '$destination'");
            }
        }

        $args = $parts;
        $this->validateActionArguments($controllerClass, $action, $args);

        return new Route($controllerClass, $action, $args);
    }
}
