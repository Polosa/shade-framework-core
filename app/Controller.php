<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Base Controller
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
abstract class Controller
{
    /**
     * Service Provider
     *
     * @var \Shade\ServiceProvider
     */
    protected $serviceProvider;

    /**
     * Request
     *
     * @var \Shade\Request
     */
    protected $request;

    /**
     * Set Service Provider
     *
     * @param \Shade\ServiceProvider $serviceProvider
     *
     * @return \Shade\Controller
     */
    public function setServiceProvider(ServiceProvider $serviceProvider)
    {
        $this->serviceProvider = $serviceProvider;

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
     * Get Service Provider
     *
     * @return \Shade\ServiceProvider
     */
    protected function serviceProvider()
    {
        return $this->serviceProvider;
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
     * Render template and get Response
     *
     * @param string|array $templates Path to template or array of paths to template and layouts
     * @param array        $data      Data for templates
     *
     * @return \Shade\Response
     */
    public function render($templates, array $data = array())
    {
        $content = $this->serviceProvider->getView()->render($templates, $data);
        $response = new Response();
        $response->setContent($content);

        return $response;
    }
}
