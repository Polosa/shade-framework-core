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
        $dir = dirname(__DIR__);
        passthru("$dir/shade-cli.php new");
    }
}