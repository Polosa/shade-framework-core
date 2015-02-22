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
 * CLI Request
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Cli extends Request
{
    /**
     * Constructor
     *
     * @param array $server
     */
    public function __construct(
        array $server = array()
    ) {
        $this->server = $server;
        $this->argv = isset($server['argv']) ? $server['argv'] : array();
    }

    /**
     * Make new Request using GLOBALS
     *
     * @return \Shade\Request\Cli
     */
    public static function makeFromGlobals()
    {
        return new self(
            $_SERVER
        );
    }
}
