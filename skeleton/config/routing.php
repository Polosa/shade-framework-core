<?php

/**
 * ShadeApp
 */

/* Routing */

/**
 * @var \ShadeApp\App $app
 */

$router = $app->getRouter();
if ($router instanceof \Shade\Router\Wildcard) {
    $router->addMapping('/', '\\ShadeApp\\Controller\\Index', 'indexAction');
}