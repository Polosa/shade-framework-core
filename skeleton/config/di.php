<?php

/**
 * ShadeApp
 */

use Shade\ServiceContainer;
use Shade\ServiceProvider\ViewPhp as ViewPhpServiceProvider;
use Shade\ServiceProvider\RouterWildcard as RouterWildcardServiceProvider;

/* Dependency Injection */

/**
 * @var \ShadeApp\App $app
 */
$appConfig = $app->getConfig();

$app->registerService(ServiceContainer::SERVICE_ROUTER, new RouterWildcardServiceProvider());
$app->registerService(ServiceContainer::SERVICE_VIEW, new ViewPhpServiceProvider($app));

$app->getControllerDispatcher()
    ->setArgumentValue('\\ShadeApp\\Controller\\Index', 'indexAction', 'appName', $app->getAppNamespace())
    ->setArgumentValue('\\ShadeApp\\Controller\\Profiler', 'outputAction', 'startTime', $app->getStartTime())
    ->setArgumentValue('\\ShadeApp\\Controller\\Profiler', 'outputAction', 'showProfiler', !empty($appConfig['debug']['profiler_enabled']));