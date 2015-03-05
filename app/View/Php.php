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
 * Shade View "PHP"
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Php extends View implements ViewInterface
{

    /**
     * Templates path
     *
     * @var string
     */
    protected $templatesPath;

    /**
     * Helpers
     *
     * @var array
     */
    protected static $helpers = array();

    /**
     * Constructor
     *
     * @param string $templatesPath Path to templates root directory
     */
    public function __construct($templatesPath)
    {
        $this->setTemplatesPath($templatesPath);
    }

    /**
     * Set templates path
     *
     * @param string $templatesPath Path to templates root directory
     */
    public function setTemplatesPath($templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }

    /**
     * Set helper function
     *
     * @param string   $name     Name
     * @param callable $callback Callback function
     *
     * @return \Shade\View
     */
    public function setHelper($name, $callback)
    {
        self::$helpers[$name] = $callback;

        return $this;
    }

    /**
     * Get helper
     *
     * @param string $name Helper name
     *
     * @return callable
     */
    protected static function getHelper($name)
    {
        return self::$helpers[$name];
    }

    /**
     * Render template
     *
     * @param string|array $templates Path to template or array of paths to template and layouts
     * @param array        $data      Data for templates
     *
     * @throws \Shade\Exception
     *
     * @return string
     */
    public function render($templates, array $data = array())
    {
        $__templates = (array) $templates;

        extract($data);
        ob_start();

        foreach ($__templates as $__template) {
            $__template = $this->templatesPath.$__template;

            if (is_readable($__template) && is_file($__template)) {
                include $__template;
            } else {
                ob_end_clean();
                throw new \Shade\Exception('Template file "'.$__template.'" does not exists');
            }
            $content = ob_get_clean();
        }

        return $content;
    }

    /**
     * Helper for sub-templates includes
     *
     * @param string|array $templates Path to template or array of paths to template and layouts
     * @param array        $data      Data for templates
     *
     * @return string
     */
    protected static function inc($templates, array $data = array())
    {
        return call_user_func_array(self::getHelper('inc'), func_get_args());
    }

    /**
     * Helper includes result of virtual request
     *
     * @param string $controller Controller name
     * @param string $action     Action name
     * @param array  $args       Action arguments
     * @param array  $get        GET-parameters
     *
     * @return string
     */
    protected static function exe($controller, $action, array $args = array(), array $get = array())
    {
        return call_user_func_array(self::getHelper('exe'), func_get_args());
    }

    /**
     * URL-helper
     *
     * @param string $controller Controller name
     * @param string $action     Action name
     * @param array  $args       Action arguments
     * @param array  $get        GET-parameters
     *
     * @return string
     */
    protected static function url($controller, $action, array $args = array(), array $get = array())
    {
        return call_user_func_array(self::getHelper('url'), func_get_args());
    }
}
