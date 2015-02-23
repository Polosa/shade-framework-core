<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Router;

use Shade\Request;

/**
 * Interface for Router with clean/dirty URLs support
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
interface RouterCuInterface extends RouterInterface
{
    /**
     * Check that requested URL is clean
     *
     * @param \Shade\Request\Web $request Request
     *
     * @return bool
     */
    public function isRequestedUrlClean(Request\Web $request);

    /**
     * Enable clean URLs
     */
    public function enableCleanUrls();

    /**
     * Disable clean URLs
     */
    public function disableCleanUrls();
}
