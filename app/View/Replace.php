<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\View;

use Shade\View;

/**
 * Shade View "Replace"
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Replace extends View implements ViewInterface
{
    /**
     * Render template
     *
     * @param string|array $__templates Path to template or array of paths to template and layouts
     * @param array        $__data      Data for templates
     *
     * @throws \Shade\Exception
     *
     * @return string
     */
    public function render($__templates, array $__data = array())
    {
        if (is_array($__templates)) {
            throw new \Shade\Exception('Rendering of multiple templates not supported by View "Replace"');
        }

        if (!is_readable($__templates) && is_file($__templates)) {
            throw new \Shade\Exception('Template file "'.$__templates.'" does not exists');
        }

        return str_replace(array_keys($__data), $__data, file_get_contents($__templates));
    }
}
