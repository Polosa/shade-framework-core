<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

use Shade\View\ViewInterface;

/**
 * Base Controller
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
abstract class Controller
{
    /**
     * Current Request
     *
     * @var \Shade\Request
     */
    protected $currentRequest;

    /**
     * Current Route
     *
     * @var \Shade\Route
     */
    protected $currentRoute;

    /**
     * Primary Request
     *
     * @var \Shade\Request
     */
    protected $primaryRequest;

    /**
     * Primary Route
     *
     * @var \Shade\Route
     */
    protected $primaryRoute;

    /**
     * View
     *
     * @var \Shade\View\ViewInterface
     */
    private $view;

    /**
     * Controller Dispatcher
     *
     * @var \Shade\ControllerDispatcher
     */
    private $controllerDispatcher;

    /**
     * Set Controller Dispatcher
     *
     * @param \Shade\ControllerDispatcher $controllerDispatcher
     *
     * @return \Shade\Controller
     */
    public function setControllerDispatcher(ControllerDispatcher $controllerDispatcher)
    {
        $this->controllerDispatcher = $controllerDispatcher;

        return $this;
    }

    /**
     * Set current Request
     *
     * @param \Shade\Request $request
     *
     * @return \Shade\Controller
     */
    public function setCurrentRequest(Request $request)
    {
        $this->currentRequest = $request;

        return $this;
    }

    /**
     * Set primary Request
     *
     * @param \Shade\Request $request
     *
     * @return \Shade\Controller
     */
    public function setPrimaryRequest(Request $request)
    {
        $this->primaryRequest = $request;

        return $this;
    }

    /**
     * Set current Route
     *
     * @param \Shade\Route $route
     *
     * @return \Shade\Controller
     */
    public function setCurrentRoute(Route $route)
    {
        $this->currentRoute = $route;

        return $this;
    }

    /**
     * Set primary Route
     *
     * @param \Shade\Route $route
     *
     * @return \Shade\Controller
     */
    public function setPrimaryRoute(Route $route)
    {
        $this->primaryRoute = $route;

        return $this;
    }

    /**
     * Set View
     *
     * @param \Shade\View\ViewInterface $view
     *
     * @return \Shade\Controller
     */
    public function setView(ViewInterface $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get Request
     *
     * @param bool $primary Primary Request
     *
     * @return \Shade\Request
     */
    protected function getRequest($primary = false)
    {
        if ($primary) {
            return $this->primaryRequest;
        } else {
            return $this->currentRequest;
        }
    }

    /**
     * Get Route
     *
     * @param bool $primary Primary Route
     *
     * @return \Shade\Route
     */
    protected function getRoute($primary = false)
    {
        if ($primary) {
            return $this->primaryRoute;
        } else {
            return $this->currentRoute;
        }
    }

    /**
     * Render template and get Response
     *
     * @param string|array $templates Path to template or array of paths to template and layouts
     * @param array        $data      Data for templates
     *
     * @return \Shade\Response
     */
    protected function render($templates, array $data = array())
    {
        $content = $this->view->render($templates, $data);
        $response = new Response();
        $response->setContent($content);

        return $response;
    }

    /**
     * Dispatch
     *
     * @param Request $request
     *
     * @throws \Shade\Exception
     *
     * @return \Shade\Response
     */
    protected function dispatch(Request $request)
    {
        return $this->controllerDispatcher->dispatch($request);
    }
}
