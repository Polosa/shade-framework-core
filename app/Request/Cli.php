<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Request;

use Shade\Request;
use Shade\ServiceProvider;

/**
 * CLI Request
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Cli extends Request
{
    /**
     * Default destination
     */
    const DEFAULT_DESTINATION = 'cli/index';

    /**
     * Constructor
     *
     * @param \Shade\ServiceProvider $serviceProvider
     * @param array                  $server
     */
    public function __construct(
        ServiceProvider $serviceProvider,
        array $server = array()
    ) {
        $this->serviceProvider = $serviceProvider;

        $this->server = $server;
        $this->argv = isset($server['argv']) ? $server['argv'] : array();
    }

    /**
     * Make new Request using GLOBALS
     *
     * @param \Shade\ServiceProvider $serviceProvider
     *
     * @return \Shade\Request\Cli
     */
    public static function makeFromGlobals(ServiceProvider $serviceProvider)
    {
        return new self(
            $serviceProvider,
            $_SERVER
        );
    }
}
