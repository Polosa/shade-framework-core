<?php

/**
 * Shade
 *
 * @version 0.1
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
     * Convert string in CamelCase to string in snake_case
     *
     * @param string $str
     *
     * @return string
     */
    public static function convertCamelToSnake($str)
    {
        $str[0] = strtolower($str[0]);

        return preg_replace('/([A-Z])/e', "'_' . strtolower('\\1')", $str);
    }

    /**
     * Convert string in snake_case to string in CamelCase
     *
     * @param string $str
     * @param bool   $capitaliseFirstChar
     *
     * @return string
     */
    public static function convertSnakeToCamel($str, $capitaliseFirstChar = false)
    {
        if ($capitaliseFirstChar) {
            $str[0] = strtoupper($str[0]);
        }

        return preg_replace('/_([a-z])/e', "strtoupper('\\1')", $str);
    }

    //TODO StudlyCase

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
