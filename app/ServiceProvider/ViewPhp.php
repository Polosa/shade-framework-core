<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\ServiceProvider;

use Shade\ServiceProviderInterface;
use Shade\App;
use Shade\View\Php as ViewPhpService;
use Shade\Request\Virtual as VirtualRequest;

/**
 * View "PHP" Service Provider
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ViewPhp implements ServiceProviderInterface
{
    /**
     * Templates directory
     */
    const TEMPLATES_DIR = 'templates';

    /**
     * Application
     *
     * @var App
     */
    protected $app;

    /**
     * Constructor
     *
     * @param App $app Application
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Instantiate Service
     *
     * @return \Shade\View\Php
     */
    public function instantiate()
    {
        $view = new ViewPhpService($this->app->getAppDir().'/'.self::TEMPLATES_DIR.'/');

        $view->setHelper(
            'inc',
            function ($templates, array $data = array()) use ($view) {
                return $view->render($templates, $data);
            }
        )->setHelper(
            'exe',
            function ($controller, $action, array $get = array()) {
                $request = new VirtualRequest($controller, $action, $get);

                return $this->app->getControllerDispatcher()->dispatch($request)->getContent();
            }
        )->setHelper(
            'url',
            function ($controller, $action, array $args = array(), array $get = array()) {
                return $this->app->getRouter()->buildUrl($controller, $action, $args, $get);
            }
        );

        return $view;
    }
}
