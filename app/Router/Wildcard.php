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
 * Wildcard Router
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Wildcard extends Router implements RouterCuInterface
{
    /**
     * Route mapping
     *
     * @var array
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
        $destinationPieces = explode('/', $destination);

        $mappingNode = $this->routeMapping;
        $argValues = array();
        foreach($destinationPieces as $destinationPiece) {
            if (isset($mappingNode[$destinationPiece])) {
                $mappingNode = $mappingNode[$destinationPiece];
            } elseif (isset($mappingNode['_wildcard'])) {
                $argValues[] = $destinationPiece;
                $mappingNode = $mappingNode['_wildcard'];
            } else {
                throw new Exception("Route mapping not found for destination '{$destination}'");
            }
        }

        if (!isset($mappingNode['_mapping']) || !($mappingNode['_mapping'] instanceof Route\Mapping)) {
            throw new Exception("Route mapping not found for destination '{$destination}'");
        }

        /**
         * @var Route\Mapping $mapping;
         */
        $mapping = $mappingNode['_mapping'];

        $patternPieces = explode('/', $mapping->destinationPattern());
        $routeArgs = array();
        foreach ($patternPieces as $piece) {
            if($this->isWildcard($piece)) {
                $argName = substr($piece, 1, -1);
                $routeArgs[$argName] = array_shift($argValues);
            }
        }

        $this->validateRouteMapping($mapping);

        return new Route($mapping->controller(), $mapping->action(), $routeArgs);
    }

    /**
     * Check if destination piece is a wildcard
     *
     * @param string $piece Destination piece
     *
     * @return bool
     */
    protected function isWildcard($piece)
    {
        return
            strlen($piece) > 2
            && substr($piece, 0, 1) == '{'
            && substr($piece, -1)  == '}';
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

        $this->reverseMapping[$controllerClass][$actionName] = $mapping;

        $destinationPieces = explode('/', $destinationPattern);

        $mappingNode = &$this->routeMapping;
        foreach($destinationPieces as $destinationPiece) {
            if ($this->isWildcard($destinationPiece)) {
                if (!isset($mappingNode['_wildcard'])) {
                    $mappingNode['_wildcard'] = null;
                }
                $mappingNode = &$mappingNode['_wildcard'];
            } else {
                if (!isset($mappingNode[$destinationPiece])) {
                    $mappingNode[$destinationPiece] = null;
                }
                $mappingNode = &$mappingNode[$destinationPiece];
            }
        }
        $mappingNode['_mapping'] = $mapping;

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
        foreach ($args as $argName => $argValue) {
            $destinationPattern = str_replace("{{$argName}}", $argValue, $destinationPattern, $count);
            if ($count < 1) {
                throw new Exception("Provided argument '{$argName}' doesn't match any URL wildcard");
            }
        }

        return $destinationPattern;
    }
}
