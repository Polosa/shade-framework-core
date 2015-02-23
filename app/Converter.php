<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Converter
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Converter
{
    /**
     * Convert size in bytes to human readable format
     *
     * @param integer $bytes     Size in Bytes
     * @param integer $precision Number of decimal digits to round to
     *
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, $precision).' KB';
        } elseif ($bytes < 1073741824) {
            return round($bytes / 1048576, $precision).' MB';
        } elseif ($bytes < 1099511627776) {
            return round($bytes / 1073741824, $precision).' GB';
        } else {
            return round($bytes / 1099511627776, $precision).' TB';
        }
    }
}
