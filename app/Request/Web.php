<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Request;

use Shade\Request;

/**
 * Web Request
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Web extends Request
{
    /**
     * Script name
     */
    const SCRIPT_NAME = '/index.php';

    /**
     * Default destination
     */
    const DEFAULT_DESTINATION = 'index/index';

    /**
     * Constructor
     *
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $cookie
     * @param array $files
     * @param array $env
     */
    public function __construct(
        array $server = array(),
        array $get = array(),
        array $post = array(),
        array $cookie = array(),
        array $files = array(),
        array &$env = array()
    ) {
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->cookie = $cookie;
        $this->files = $files;
        $this->env = $env;
    }

    /**
     * Make new Request using GLOBALS
     *
     * @return \Shade\Request\Web
     */
    public static function makeFromGlobals()
    {
        return new self(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_ENV
        );
    }
}
