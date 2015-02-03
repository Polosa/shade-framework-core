<?php

/**
 * ShadeApp
 */

use Shade\ServiceContainer;
use Shade\ServiceProvider\ViewPhp as ViewPhpServiceProvider;
use Shade\ServiceProvider\RouterRegex as RouterRegexServiceProvider;

/* Dependency Injection */

/**
 * @var \ShadeApp\App $app
 */
$appConfig = $app->getConfig();

$app->registerService(ServiceContainer::SERVICE_ROUTER, new RouterRegexServiceProvider($app));
$app->registerService(ServiceContainer::SERVICE_VIEW, new ViewPhpServiceProvider($app));

$app->getControllerDispatcher()
    ->setArgumentValue('\\ShadeApp\\Controller\\Index', 'indexAction', 'appName', $app->getAppNamespace())
    ->setArgumentValue('\\ShadeApp\\Controller\\Profiler', 'outputAction', 'startTime', $app->getStartTime())
    ->setArgumentValue('\\ShadeApp\\Controller\\Profiler', 'outputAction', 'debugMode', !empty($appConfig['debug']['debug_mode']));