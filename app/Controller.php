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
     * Request
     *
     * @var \Shade\Request
     */
    protected $request;

    /**
     * Route
     *
     * @var \Shade\Route
     */
    protected $route;

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
     * Set Request
     *
     * @param \Shade\Request $request
     *
     * @return \Shade\Controller
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set Route
     *
     * @param \Shade\Route $route
     *
     * @return \Shade\Controller
     */
    public function setRoute(Route $route)
    {
        $this->route = $route;

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
     * @return \Shade\Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Get Route
     *
     * @return \Shade\Route
     */
    protected function getRoute()
    {
        return $this->route;
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
