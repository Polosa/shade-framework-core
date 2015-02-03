<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Request
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
abstract class Request
{
    /**
     * SERVER
     *
     * @var array
     */
    protected $server = array();

    /**
     * GET
     *
     * @var array
     */
    protected $get = array();

    /**
     * POST
     *
     * @var array
     */
    protected $post = array();

    /**
     * COOKIE
     *
     * @var array
     */
    protected $cookie = array();

    /**
     * FILES
     *
     * @var array
     */
    protected $files = array();

    /**
     * ENV
     *
     * @var array
     */
    protected $env = array();

    /**
     * Array of arguments passed to script
     *
     * @var array
     */
    protected $argv = array();

    /**
     * Get SERVER
     *
     * @return array
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Get GET
     *
     * @return array
     */
    public function getGet()
    {
        return $this->get;
    }

    /**
     * Get POST
     *
     * @return array
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Get Cookie
     *
     * @return array
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * Get Files
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get Environment
     *
     * @return array
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Get array of arguments passed to script
     *
     * @return array
     */
    public function getArgv()
    {
        return $this->argv;
    }
}
