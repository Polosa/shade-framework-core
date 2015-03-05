<?php

/**
 * Shade
 *
 * @package Shade
 * @version 1.0.0
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

$app->registerService(ServiceContainer::SERVICE_ROUTER, new RouterWildcardServiceProvider($app));
$app->registerService(ServiceContainer::SERVICE_VIEW, new ViewPhpServiceProvider($app));
$app->registerService('view.replace', new ViewReplaceServiceProvider());
$app->setService(ServiceContainer::SERVICE_LOGGER, $logger);

$app->getControllerDispatcher()
    ->setArgumentValue('\\Shade\\Controller\\Profiler', 'outputAction', 'startTime', $app->getStartTime())
    ->setArgumentValue('\\Shade\\Controller\\Profiler', 'outputAction', 'showProfiler', $appConfig->debug->profilerEnabled->getValue())
    ->setArgumentValue('\\Shade\\Controller\\Cli', 'newAction', 'appDir', $app->getAppDir())
    ->bindService('\\Shade\\Controller\\Cli', 'newAction', 'viewReplace', 'view.replace');