<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

use Psr\Log\LoggerAwareTrait;

/**
 * Router
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
abstract class Router
{
    use LoggerAwareTrait;

    /**
     * Clean URLs
     *
     * @var bool
     */
    protected $cleanUrlsEnabled = false;

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
            $this->logger->debug('Routing of web request', ['REQUEST_URI' => $server['REQUEST_URI']]);
            $urlComponents = explode('?', $server['REQUEST_URI']);
            $destination = reset($urlComponents);
            if ($destination && (strpos($destination, $request::SCRIPT_NAME) === 0)) {
                $destination = substr($destination, strlen($request::SCRIPT_NAME));
            }
        } elseif ($request instanceof Request\Cli) {
            $argv = $request->getArgv();
            $destination = isset($argv[1]) ? $argv[1] : '/';
            $this->logger->debug('Routing of CLI request', ['argv' => $argv]);
        } elseif ($request instanceof Request\Virtual) {
            $this->logger->debug(
                'Routing of virtual request',
                ['controller' => $request->getController(), 'action' => $request->getAction()]
            );
            return new Route($request->getController(), $request->getAction());
        } else {
            throw new Exception('Request type not supported');
        }

        $destination = trim($destination, '/');
        $destination = empty($destination) ? '/' : $destination;

        $this->logger->debug('Searching for corresponding route', ['destination' => $destination]);

        return $this->getRoute($destination);
    }

    /**
     * Get Route for given destination
     *
     * @param $destination
     *
     * @return Route
     */
    abstract protected function getRoute($destination);

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
    abstract protected function buildDestination($controller, $action, array $args = array());

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
    public function buildUrl($controller, $action, array $args = array(), array $get = array())
    {
        $url = $this->buildDestination($controller, $action, $args);
        if (!$this->cleanUrlsEnabled()) {
            $url = Request\Web::SCRIPT_NAME.$url;
        }
        if ($get) {
            $url .= '?'.http_build_query($get);
        }

        return $url;
    }

    /**
     * Check that requested URL is clean
     *
     * @param \Shade\Request\Web $request Request
     *
     * @throws \Shade\Exception
     *
     * @return bool
     */
    public function isRequestedUrlClean(Request\Web $request)
    {
        $server = $request->getServer();
        if (!isset($server['REQUEST_URI'])) {
            throw new Exception('REQUEST_URI is not set');
        }

        return strpos($server['REQUEST_URI'], $request::SCRIPT_NAME) !== 0;
    }

    /**
     * Enable clean URLs
     */
    public function enableCleanUrls()
    {
        $this->cleanUrlsEnabled = true;
    }

    /**
     * Disable clean URLs
     */
    public function disableCleanUrls()
    {
        $this->cleanUrlsEnabled = false;
    }

    /**
     * Check that clean URLs enabled
     *
     * @return boolean
     */
    protected function cleanUrlsEnabled()
    {
        return $this->cleanUrlsEnabled;
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
            throw new Exception("Controller '{$mapping->controller()}' not found");
        }

        if (!method_exists($mapping->controller(), $mapping->action())) {
            throw new Exception("Action '{$mapping->action()}' does not exist in controller '{$mapping->controller()}'");
        }
    }
}
