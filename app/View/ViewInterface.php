<?php

/**
 * Shade
 *
 * @version 1.0.0
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
     * @param string|array $templates Path to template or array of paths to template and layouts
     * @param array        $data      Data for templates
     *
     * @return string
     */
    public function render($templates, array $data = array());
}
