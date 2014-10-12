<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\View;

use Shade\View;
use Shade\ServiceProvider;
use Shade\Request\Virtual as VirtualRequest;

/**
 * Shade View "PHP"
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Php extends View
{
    /**
     * Constructor
     *
     * @param \Shade\ServiceProvider $serviceProvider
     */
    public function __construct(ServiceProvider $serviceProvider)
    {
        parent::__construct($serviceProvider);

        $view = $this;

        $this->setHelper(
            'inc',
            function ($templates, array $data = array()) use ($view) {
                return $view->render($templates, $data);
            }
        );

        $this->setHelper(
            'exe',
            function ($controller, $action, array $args = array(), array $get = array()) use ($serviceProvider) {
                $request = new VirtualRequest($serviceProvider, $controller, $action, $args, $get);
                return $serviceProvider->getApp()->execute($request)->getContent();
            }
        );

        $this->setHelper(
            'url',
            function ($controller, $action, array $args = array(), array $get = array()) use ($serviceProvider) {
                return $serviceProvider->getRouter()->buildUrl($controller, $action, $args, $get);
            }
        );
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

        extract($__data);
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
