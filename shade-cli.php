#!/usr/bin/env php
<?php

/**
 * Shade CLI runner
 *
 * @package Shade
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

require_once 'vendor/autoload.php';
$app = new \Shade\App();

require_once 'config/di.php';

$router = $app->getRouter();
if ($router instanceof \Shade\Router\Wildcard) {
    $router->addMapping('/', '\\Shade\\Controller\\Cli', 'indexAction');
    $router->addMapping('help', '\\Shade\\Controller\\Cli', 'helpAction');
    $router->addMapping('new', '\\Shade\\Controller\\Cli', 'newAction');
    $router->addMapping('run', '\\Shade\\Controller\\Cli', 'runAction');
}

$app->run();
