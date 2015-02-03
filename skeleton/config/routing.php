<?php

/**
 * ShadeApp
 */

/* Routing */

/**
 * @var \ShadeApp\App $app
 */
$router = $app->getRouter();
if ($router instanceof \Shade\Router\Regex) {
    $router->addMapping('~^/$~', '\\ShadeApp\\Controller\\Index::indexAction');
}