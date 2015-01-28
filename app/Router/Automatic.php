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
class Automatic extends Router implements RouterCuInterface
{
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
        } elseif (is_file("{$controllerPath}{$this->defaultController}.php")) {
            $controllerFile = $this->defaultController;
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
