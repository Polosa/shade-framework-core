<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Composer
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Composer
{
    /**
     * Composer script
     */
    public static function generateSkeleton()
    {
        $app = new App();
        $serviceProvider = new ServiceProvider($app);
        $request = new Request\Cli($serviceProvider, array('argv' => array(1 => 'new')));
        $app->output($app->execute($request));
    }
}