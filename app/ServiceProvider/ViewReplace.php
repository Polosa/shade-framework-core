<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\ServiceProvider;

use Shade\ServiceProviderInterface;
use Shade\View\Replace as ViewReplaceService;

/**
 * View "Replace" Service Provider
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ViewReplace implements ServiceProviderInterface
{
    /**
     * Instantiate Service
     *
     * @return \Shade\View\Replace
     */
    public function instantiate()
    {
        return $view = new ViewReplaceService();
    }
}
