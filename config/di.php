<?php

/**
 * Shade
 *
 * @package Shade
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

use Shade\ServiceContainer;
use Shade\ServiceProvider\ViewPhp as ViewPhpServiceProvider;
use Shade\ServiceProvider\RouterWildcard as RouterWildcardServiceProvider;
use Shade\ServiceProvider\ViewReplace as ViewReplaceServiceProvider;

/* Dependency Injection */

/**
 * @var \Shade\App $app
 */
$appConfig = $app->getConfig();

$app->registerService(ServiceContainer::SERVICE_ROUTER, new RouterWildcardServiceProvider());
$app->registerService(ServiceContainer::SERVICE_VIEW, new ViewPhpServiceProvider($app));
$app->registerService('view_replace', new ViewReplaceServiceProvider());

$app->getControllerDispatcher()
    ->setArgumentValue('\\Shade\\Controller\\Profiler', 'outputAction', 'startTime', $app->getStartTime())
    ->setArgumentValue('\\Shade\\Controller\\Profiler', 'outputAction', 'showProfiler', !empty($appConfig['debug']['profiler_enabled']))
    ->setArgumentValue('\\Shade\\Controller\\Cli', 'newAction', 'appDir', $app->getAppDir())
    ->bindService('\\Shade\\Controller\\Cli', 'newAction', 'viewReplace', 'view_replace');