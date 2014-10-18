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
        $cliDir = dirname(__DIR__);
        $phpBin = PHP_BINARY;
        passthru("$phpBin $cliDir/shade-cli.php new");
    }
}
