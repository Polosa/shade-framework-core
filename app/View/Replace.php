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
     * Layout content placeholder
     *
     * @var string
     */
    protected $layoutContentPlaceholder = self::DEFAULT_LAYOUT_CONTENT_PLACEHOLDER;

    /**
     * Default layout content placeholder
     */
    const DEFAULT_LAYOUT_CONTENT_PLACEHOLDER = '%content%';

    /**
     * Set layout content placeholder
     *
     * @param string $placeholder Placeholder
     */
    public function setLayoutContentPlaceholder($placeholder) {
        $this->layoutContentPlaceholder = $placeholder;
    }

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
        $__templates = (array) $__templates;

        foreach ($__templates as $__template) {
            if (!is_readable($__template) || !is_file($__template)) {
                throw new \Shade\Exception('Template file "'.$__template.'" does not exists');
            }
            $content = str_replace(array_keys($__data), $__data, file_get_contents($__template));
            $__data[$this->layoutContentPlaceholder] = $content;
        }

        return $content;
    }
}
