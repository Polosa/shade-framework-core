<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\View;

/**
 * View interface
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
interface ViewInterface
{
    /**
     * Render template(s)
     *
     * @param string|array $__templates Path to template or array of paths to template and layouts
     * @param array        $__data      Data for templates
     *
     * @return string
     */
    public function render($__templates, array $__data = array());
}
